<?php

namespace App\Http\Controllers;

use App\Models\InvoicePrinting;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;

class SettingController extends Controller
{
    protected $settings;
    protected $collection;
    public function __construct(){
        $this->settings = $this->master();
        $this->collection = collect($this->settings);
    }
    public function index(){
        return view('settings/setting-index');
    }

    public function getGeneral()
    {
        $data = $this->collection->only(
            [
                'app_url',
                'tva',
                'app_name',
                'app_https',
                'auth_code_for_e_kiosks',
                'app_address',
                'app_phone',
                'socials',
                'wifi_name',
                'web',
                'wifi_password',
            ]
        );
        return view('settings/general-index', compact('data'));
    }

    public function locationsForAdmin(Request $request){
        $data = $request->get('locale');
        if (isset($data)){
            session()->put('localization_for_changes_data', Location::where('id', $data)->first());
        }else{
            session()->remove('localization_for_changes_data');
        }
        return back();
    }
    public function setGeneral(Request $request){
        $data = $request->all();
        $this->settings->update($data);
    }
    public function getAppearance(){
        return;  //this method is out of action
        $icon = $this->settings->getImage();
        $secondIcon = $this->settings->getSecondImage();
        return view('settings/appearance-index', compact('icon', 'secondIcon'));
    }
    public function setAppearance(Request $request){
        return;  //this controller is out of action
        $rules = [];
        if ($this->settings->app_icon == null) {
            $rules['icon'] = 'required|extensions:jpg,jpeg,png,svg,webp';
        }
        if ($this->settings->app_second_icon == null) {
            $rules['second_icon'] = 'required|extensions:jpg,jpeg,png,svg,webp';
        }
        $validated = $request->validate($rules);
        if ($request->file('icon')) {
            $validated['app_icon'] = $request->file('icon')
                ->store('appearance/icon', 'public');
            if (!empty($this->settings->app_icon)) {
                Storage::disk('public')->delete($this->settings->app_icon);
            }
        }
        if ($request->file('second_icon')) {
            $validated['app_second_icon'] = $request->file('second_icon')
                ->store('appearance/second_icon', 'public');
            if (!empty($this->settings->app_second_icon)) {
                Storage::disk('public')->delete($this->settings->app_second_icon);
            }
        }
        $this->settings->update($validated);
        return response()->json(
            ['message' => __('Settings updated successfully')]
        );
    }
    public function getClientAppearance(){
        $clientIcon = $this->settings->getClientImage();
        return view('settings/client-appearance-index', compact('clientIcon'));
    }
    public function setClientAppearance(Request $request){
        $validated = $request->validate([
            'client_icon' => 'required|extensions:jpg,jpeg,png,svg,webp'
        ]);
        if ($request->file('client_icon')) {
            $validated['app_client_icon'] = $request->file('client_icon')
                ->store('appearance/client_icon', 'public');
            if (!empty($this->settings->app_client_icon)) {
                Storage::disk('public')->delete($this->settings->app_client_icon);
            }
        }
        $this->settings->update($validated);
        return response()->json(
            ['message' => __('Settings updated successfully')]
        );
    }
    public function getCurrency(){
        $data = $this->collection->only([
            'currency_symbol',
            'currency_symbol_on_left'
        ]);
        return view('settings/currency-index', compact('data'));
    }
    public function setCurrency(Request $request){
        $this->settings->update($request->all());
    }
    public function getAuthentication(){
        return view('settings/authentication-index');
    }
    public function setAuthentication(){
        dd('setAuthentication');
    }
    public function getCaptcha(){
        return view('settings/captcha-index');
    }
    public function setCaptcha(){
        dd('setCaptcha');
    }
    public function getLocalization(){
        $data = $this->collection->only([
            'app_timezone',
            'app_date_format',
            'default_language'
        ]);
        $locations = Location::get();
        return view('settings/localization-index', compact('data', 'locations'));
    }
    public function getInvoicePrinting(){
        $invoicePrinting = InvoicePrinting::first();
        return view('settings/invoicePrinting-index', compact('invoicePrinting'));
    }
    public function setInvoicePrinting(Request $request){
        $data = $request->all();
        $invoicePrinting = InvoicePrinting::first();
        if ($invoicePrinting->logo_header == null){
            $request->validate([
                'image' => 'required|extensions:jpg,jpeg,png,svg,webp'
            ]);
        }
        if ($request->file('image')) {
            $data['logo_header'] = $request->file('image')
                ->store('invoicePrintings/logo', 'public');
            if (!empty($invoicePrinting->logo_header)) {
                Storage::disk('public')->delete($invoicePrinting->logo_header);
            }
        }
        $invoicePrinting->update($data);
        return response()->json(
            ['message' => __('Settings updated successfully')]
        );
    }
    public function getHelpContacts(){
        $data = Http::get('https://mgmt.aseria-pos.ch/api/getSettings')->json();
        return view('settings/help-contacts', compact('data'));
    }

    public function getBase64Image(Request $request){
        $orderId = $request->value;
        $barcode = new DNS1D();
        $barcodeImage = $barcode->getBarcodePNG((string)$orderId, 'C128');
        return response()->json($barcodeImage);
    }
    public function setHelpContacts(Request $request){
        dd($request->all());
    }
}
