<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\ZReport;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class CashRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cashRegisters = CashRegister::with('user')->get();
        return view('cash-register/cashRegister-index', compact('cashRegisters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'cash-register/cashRegister-create' : 'pop-up-locations');
        }
        return view('cash-register/cashRegister-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(CashRegister::class)],
                'key' => 'required',
                'pin' => 'required',
                'pin_for_settings' => 'required',
                'pin_to_print_reports' => 'required'
            ]);
            $data['user_id'] = auth()->id();
            CashRegister::create($data);
            return response()->json(['success', 'You have added the Cash Register']);
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
    public function edit(CashRegister $cashRegister)
    {
        return view('cash-register/cashRegister-edit', compact('cashRegister'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashRegister $cashRegister)
    {
        try {
            $data = $request->all();
            if ($cashRegister->status){
                if (!isset($data['status'])){
                    $zReport = ZReport::where(['cash_register_id' => $cashRegister->id, 'end_z_report' => null])->first();
                    if ($zReport){
                        return response()->json(['errors' => ['name' => ['.'.__('The cash register is already open for a zReport.')]]], 422);
                    }
                }
            }
            $request->validate([
                'name' => ['required',
                    Rule::unique('cash_registers')->where(function ($query) use ($request, $cashRegister) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($cashRegister->id),
                ],
                'pin' => 'required',
                'pin_for_settings' => 'required',
                'pin_to_print_reports' => 'required'
            ]);
            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            $cashRegister->update($data);
            return response()->json(['success', 'The Cash Register is updated']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashRegister $cashRegister)
    {
        $cashRegister->forceDelete();
        //        $cashRegister->delete();
        return response()->json(['success' => 'The cash register is trashed!']);
    }


    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        //        Ingredient::whereIn('id', $request->ids)->delete();
        CashRegister::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(CashRegister $cashRegister)
    {
        $cashRegister->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(CashRegister $cashRegister)
    {
        $cashRegister->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }
}
