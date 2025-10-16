<?php

namespace App\Http\Controllers;

use App\Models\AseriaManagement;
use App\Models\Location;
use App\Models\Setting;
use App\Models\User;
use dacoto\EnvSet\Facades\EnvSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;
use Illuminate\Validation\ValidationException;


class VerifyKeyController extends Controller
{
    private $url;
    private $sysMngUrl;

    public function __construct()
    {
        $this->url = rtrim(getenv('APP_URL'), '/');
//        $this->sysMngUrl = "http://127.0.0.1:8000";
        $this->sysMngUrl = "https://mgmt.aseria-pos.ch";
    }

    public function verifyKeyIndex()
    {
        $keyChecked = Http::post($this->sysMngUrl.'/api/checkKey', [
            'url' => $this->url,
            'key' => session()->get('verify-web-key'),
        ])->json();
        if (session()->get('verify-web-key') && $keyChecked['status'] == 0 && $keyChecked['key'] == session()->get('verify-web-key')) {
            return redirect()->route('create.account.index');
        }
        return view('verify-key/login-verify-key');
    }

    public function verifyKeyPost(Request $request)
    {
        $request->validate([
            'key' => 'required'
        ]);
        $keyChecked = Http::post($this->sysMngUrl.'/api/checkKey', [
            'url' => $this->url,
            'key' => $request->key,
        ])->json();
        if ($keyChecked['status'] == 0) {
            if (Setting::first()->is_setup){
                EnvSet::setKey('WEB_VERIFY_KEY', 'web');
                EnvSet::save();
                \Artisan::call('optimize:clear');
                session()->remove('verify-web-key');
                return redirect(url('/'));
            }
            session()->put('verify-web-key', $keyChecked['key']);
            return redirect()->route('create.account.index');
        }
        return back()->with('error', 'Your key is invalid or account is deactivated');
    }

    public function createAccountIndex(Request $request)
    {
        $keyChecked = Http::post($this->sysMngUrl.'/api/checkKey', [
            'url' => $this->url,
            'key' => session()->get('verify-web-key'),
        ])->json();
        if ($keyChecked['status'] == 0 && $keyChecked['key'] == session()->get('verify-web-key')) {
            return view('verify-key/create-account-admin');
        }
        return redirect()->route('create.verify.key.index');
    }

    public function createAccountPost(Request $request)
    {
        try {
            $keyChecked = Http::post($this->sysMngUrl.'/api/checkKey', [
                'url' => $this->url,
                'key' => session()->get('verify-web-key'),
            ])->json();
            if ($keyChecked['status'] == 0 && $keyChecked['key'] == session()->get('verify-web-key')) {
                $data = $request->validate([
                    'name' => 'required',
                    'email' => 'required|email',
                    'password' => 'required|min:8',
                    'business_name' => 'required',
                    'business_phone' => 'required',
                    'location' => 'required',
                    'restaurant_name' => 'required',
                ]);
                User::create([
                    'id' => Uuid::uuid4()->toString(),
                    'name' => $data['name'],
                    'random_id' => time(),
                    'email' => $data['email'],
                    'address' => "test",
                    'avatar' => "test",
                    'password' => Hash::make($data['password']),
                    'status' => true,
                    'role_id' => config('constants.role.adminId'),
                    'pin' => "1234",
                ]);

                $setting = $this->master();
                $setting->app_name = $data['business_name'];
                $setting->app_phone = $data['business_phone'];
                $setting->update();

                Location::create([
                    'id' => config('constants.location.defaultLocationId'),
                    'name' => $data['restaurant_name'],
                    'location' => $data['location'],
                    'pos' => true,
                    'kitchen' => true,
                    'dine_in' => false,
                    'take_away' => false,
                    'delivery' => false,
                ]);

                EnvSet::setKey('APP_NAME', $data['business_name']);
                EnvSet::setKey('WEB_VERIFY_KEY', 'web');
                EnvSet::save();
                \Artisan::call('optimize:clear');
                session()->remove('verify-web-key');
                $installed = Setting::first();
                $installed->is_setup = true;
                $installed->update();
                $data = Http::get('https://mgmt.aseria-pos.ch/api/getSettings')->json();
                if (isset($data['data'])) {
                    unset($data['data']['created_at'], $data['data']['updated_at'], $data['data']['id']);
                    $aseriaManagement = AseriaManagement::first();
                    $aseriaManagement->update($data['data']);
                }
                return redirect(url('/'));
            } else {
                return redirect()->route('create.verify.key.index');
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        }
    }
}
