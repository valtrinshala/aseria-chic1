<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Location;
use App\Models\Setting;
use App\Models\SystemAsset;
use Exception;
use Illuminate\Http\Request;
class CustomerScreenController extends Controller
{
    public function index(){
        $cashRegisters = CashRegister::where('status', true)->get();
        $locations = Location::get();
        $settings = Setting::first();
        return view('pos/customer-screen', compact('cashRegisters', 'locations', 'settings'));
    }
    public function getImage(Request $request){
        $cashRegisterId = $request->get('cash_register_id', "6342b2ae-c295-443d-afe0-1d747812d1a3");
        $images = SystemAsset::where(['status' => true, 'cash_register_id' => $cashRegisterId])->get();
        return $this->response(0, "", ['images' => $images]);
    }
    public function setJsonForCustomerScreen(Request $request): void
    {
        $data = $request->all();
        $cashRegisterId = session()->get('cash_register');
        $directory = storage_path("app/customer/screen/");
        $file = $directory . $cashRegisterId . ".json";
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        $jsonData = json_encode($data['data']);
        file_put_contents($file, $jsonData);
    }

    public function getJsonForCustomerScreen(Request $request)
    {
        $cashRegisterId = $request->get('cash_register_id');
        $file = storage_path("app/customer/screen/{$cashRegisterId}.json");
        if (!file_exists($file)) {
            return response()->json(['data' => []]);
        }
        try {
            $jsonData = file_get_contents($file);
            $data = json_decode($jsonData, true);
            if ($data === null) {
                return response()->json(['data' => []]);
            }
            return response()->json(['data' => $data]);
        } catch (Exception $e) {
            return response()->json(['data' => []]);
        }
    }
    public function clearJsonForCustomerScreen(Request $request): void
    {
        $cashRegisterId = $request->get('cash_register_id');
        $file = storage_path("app/customer/screen/{$cashRegisterId}.json");
        $emptyData = [];
        $jsonData = json_encode($emptyData);
        file_put_contents($file, $jsonData);
    }
    public function response($status, $message, $data = [], $redirectUrl = null)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'redirect_uri' => $redirectUrl
        ], 200);
    }
}
