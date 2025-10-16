<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use App\Models\Ingredient;
use App\Models\PaymentMethod;
use App\Models\PrintSettings;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class PrinterSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $printerSettings = PrintSettings::get();
        return view('settings/printerSettings/printerSetting-index', compact('printerSettings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'settings/printerSettings/printerSetting-create' : 'pop-up-locations');
        }
        return view('settings/printerSettings/printerSetting-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'device_name' => ['required', new UniqueNameForLocation(PrintSettings::class)],
                'device_ip' => 'required',
                'device_port' => 'required',
                'device_type' => 'required'
            ]);
            $data = $request->all();
            if ($request->device_type == 'terminal') {
                $request->validate([
                    'terminal_type' => 'required',
                    'terminal_id' => 'required',
                    'terminal_compatibility_port' => 'required',
                    'terminal_socket_mode' => 'required'
                ]);
            }
            isset($data['device_status']) ? $data['device_status'] = 1 : $data['device_status'] = 0;
            PrintSettings::create($data);
            return response()->json(['success', __('You have added the printer settings method')]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrintSettings $printerSetting)
    {
        return view('settings/printerSettings/printerSetting-edit', compact('printerSetting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrintSettings $printerSetting)
    {
        try {
            $request->validate([
                'device_name' => ['required',
                    Rule::unique('print_settings')->where(function ($query) use ($request, $printerSetting) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($printerSetting->id),
                ],
                'device_ip' => 'required',
                'device_port' => 'required',
                'device_type' => 'required'
            ]);
            $data = $request->all();
            if ($request->device_type == 'terminal') {
                $request->validate([
                    'terminal_type' => 'required',
                    'terminal_id' => 'required',
                    'terminal_compatibility_port' => 'required',
                    'terminal_socket_mode' => 'required'
                ]);
            }else{
                $data['terminal_type'] = null;
                $data['terminal_id'] = null;
                $data['terminal_compatibility_port'] = null;
                $data['terminal_socket_mode'] = null;
            }
            isset($data['device_status']) ? $data['device_status'] = 1 : $data['device_status'] = 0;
            $printerSetting->update($data);
            return response()->json(['success', __('You have added the printer settings method')]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    public function clearDeviceFromCashRegisterOrEKiosk(PrintSettings $device){
        if ($device){
            $device->cash_register_or_e_kiosk = null;
            $device->kitchen_id = null;
            $device->cash_register_id = null;
            $device->e_kiosk_id = null;
            $device->cash_register_or_e_kiosk_assigned = false;
            $device->update();
            return $this->response(0, __("The device is cleaned"));
        }
        return $this->response(1, __("The device doesn't exist"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrintSettings $printerSetting)
    {
        $printerSetting->forceDelete();
        //        $printerSetting->delete();
        return response()->json(['success' => 'The ingredient is trashed!']);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        //        PrintSettings::whereIn('id', $request->ids)->delete();
        PrintSettings::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(PrintSettings $printerSetting)
    {
        $printerSetting->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(PrintSettings $printerSetting)
    {
        $printerSetting->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }
    private function response($status, $message, $data = [], $redirectUrl = null, $printOrder = null)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'redirect_uri' => $redirectUrl
        ], 200);
    }

}
