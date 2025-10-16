<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AseriaManagement;
use App\Models\Language;
use App\Models\Location;
use App\Models\Setting;
use App\Models\User;
use dacoto\EnvSet\Facades\EnvSet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private $url;
    private $sysMngUrl;

    public function __construct()
    {
        $this->url = rtrim(getenv('APP_URL'), '/');
//        $this->sysMngUrl = "http://127.0.0.1:8000";
        $this->sysMngUrl = "https://mgmt.aseria-pos.ch";
    }

    public function loginIndex(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        } else {
            return view('auth.login');
        }
    }

    public function login(Request $request): RedirectResponse
    {
        if (!$this->checkIfAccountIsActive()){
         return redirect(url('/'));
        }
        $this->getSettingsData();
        $data = $request->all();
        if ($request->from != 'pos' && $request->from != 'kitchen') {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
            $user = User::where('email', $data['email'])->first();
            if (!$user || (int)$user->status !== 1) {
                return redirect()->back()->withErrors([
                    'email' => 'The user is deactivated or invalid credentials.',
                ])->onlyInput('email');
            }
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $language = Language::where('locale', $user->language)->first();
                if ($language){
                    session()->put('locale', $language->locale);
                    session()->put('language_id', $language->id);
                }
                $this->checkForOneLocation($user);
                return redirect()->intended(route('home'));
            }
        } else {
            $redirectUri = $request->from == 'pos' ? 'pos.index' : 'kitchen.index';
            if ($data['type'] == 'user' && session()->get('login_without_auth')) {
                $user = User::find($data['user_id']);
                if (!$user || (int)$user->status !== 1) {
                    return redirect()->back()->withErrors([
                        'email' => 'The user is deactivated or invalid credentials.',
                    ])->onlyInput('email');
                }
                if (Auth::loginUsingId($data['user_id'])) {
                    $request->session()->regenerate();
                    $language = Language::where('locale', $user->language)->first();
                    if ($language){
                        session()->put('locale', $language->locale);
                        session()->put('language_id', $language->id);
                    }
                    $this->checkForOneLocation($user);
                    return redirect()->intended(route('home'));
                }
            } elseif ($data['type'] == 'pin' && session()->get('login_without_auth')) {
                $user = User::where('pin', $data['pin'])->first();
                if (!$user || (int)$user->status !== 1) {
                    return redirect()->back()->withErrors([
                        'email' => 'The user is deactivated or invalid credentials.',
                    ])->onlyInput('email');
                }
                if (Auth::loginUsingId($user->id)) {
                    $request->session()->regenerate();
                    $language = Language::where('locale', $user->language)->first();
                    if ($language){
                        session()->put('locale', $language->locale);
                        session()->put('language_id', $language->id);
                    }
                    $this->checkForOneLocation($user);
                    return redirect()->intended(route('home'));
                }
            } else {
                $credentials = $request->validate([
                    'email' => ['required', 'email'],
                    'password' => ['required'],
                ]);
                $user = User::where('email', $data['email'])->first();
                if (!$user || (int)$user->status !== 1) {
                    return redirect()->back()->withErrors([
                        'email' => 'The user is deactivated or invalid credentials.',
                    ])->onlyInput('email');
                }
                if (Auth::attempt($credentials)) {
                    $request->session()->regenerate();
                    $language = Language::where('locale', $user->language)->first();
                    if ($language){
                        session()->put('locale', $language->locale);
                        session()->put('language_id', $language->id);
                    }
                    session()->put('login_without_auth', true);
                    $this->checkForOneLocation($user);
                    return redirect()->route('home');
                }
            }
        }
        return redirect()->back()->withErrors([
            'email' => 'The user is deactivated or invalid credentials.',
        ]);
    }

    private function checkForOneLocation($user):void {
        if ($user?->role_id == config('constants.role.adminId') && Location::count() == 1){
            session()->put('localization_for_changes_data', Location::first());
        }
    }
    private function getSettingsData(): void{
        try {
            $data = Http::get('https://mgmt.aseria-pos.ch/api/getSettings')->json();
            if (isset($data['data'])) {
                unset($data['data']['created_at'], $data['data']['updated_at'], $data['data']['id']);
                $aseriaManagement = AseriaManagement::first();
                $aseriaManagement->update($data['data']);
            }
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }

    }

    private function checkIfAccountIsActive(){
        try {
            $keyChecked = Http::post($this->sysMngUrl.'/api/checkIfAccountIsActive', [
                'url' => $this->url
            ])->json();
            if ($keyChecked['status'] == 1) {
                EnvSet::setKey('WEB_VERIFY_KEY', 'web_verify');
                EnvSet::save();
                \Artisan::call('optimize:clear');
                return false;
            }
            return true;
        }catch (\Exception $exception){
            Log::error('Error checking account status: '.$exception->getMessage());
            return true;
        }
    }

    private function loginFromEmail($request, $redirect)
    {

    }

    public function loginPos()
    {
        $users = null;
        if (session()->get('login_without_auth')) {
            $users = User::where('role_id', config('constants.role.orderTakerId'))->get();
        }
        $from = 'pos';
        return view('pos.login-pos-index', compact('users', 'from'));
    }


    /**
     * User logout and delete user access token
     *
     * @return RedirectResponse
     */
    public function logout(Request $request, $endZReport = false): RedirectResponse
    {
        $sessionLoginPos = session()->get('login_without_auth');
        $cashRegisterData = session()->get('cash_register_data');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        if ($sessionLoginPos || $endZReport) {
            session()->put('login_without_auth', $sessionLoginPos);
        }
        if ($request->from == 'pos') {
            session()->put('cash_register', $cashRegisterData->id);
            session()->put('cash_register_data', $cashRegisterData);
            return redirect()->route('login.pos');
        }elseif($request->from == 'kitchen'){
            return redirect()->route('login.kitchen');
        }
        return redirect()->route('login.index');
    }

    public function loginKitchen()
    {
        $users = null;
        if (session()->get('login_without_auth')) {
            $users = User::where('role_id', config('constants.role.chefId'))->get();
        }
        $from = 'kitchen';
        return view('pos.login-pos-index', compact('users', 'from'));
    }
}
