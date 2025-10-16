<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Tax::orderBy('created_at', 'asc')->get();
        return view('settings/taxes/tax-index', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'settings/taxes/tax-create' : 'pop-up-locations');
        }
        return view('settings/taxes/tax-create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        isset($data['tax_fix_percentage']) ? $data['tax_fix_percentage'] = 'percentage' : $data['tax_fix_percentage'] = 'fix';
        try {
            $commonRules = [
                'name' => ['required', new UniqueNameForLocation(Tax::class)],
                'tax_rate' => 'required',
                'tax_id' => ['required', 'regex:/^[A-Z]$/', new UniqueNameForLocation(Tax::class)]
            ];
            if ($data['type']) {
                $commonRules['type'] = [new UniqueNameForLocation(Tax::class)];
            }
            $request->validate($commonRules);
            Tax::create($data);
            return response()->json(['success', 'You have added the tax']);
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
    public function edit(Tax $tax)
    {
        return view('settings/taxes/tax-edit', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        try {
            $data = $request->all();
            $commonRules = [
                'name' => ['required',
                    Rule::unique('taxes')->where(function ($query) use ($request, $tax) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($tax->id),
                ],
                'tax_rate' => 'required',
                'tax_id' => ['required', 'regex:/^[A-Z]$/',
                    Rule::unique('taxes')->where(function ($query) use ($request, $tax) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($tax->id),
                ]
            ];
            if ($data['type']) {
                $commonRules['type'] = [Rule::unique('taxes')->where(function ($query) use ($request, $tax) {
                    return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                        ->orWhere('location_id', null);
                })->ignore($tax->id)];
            }
            $request->validate($commonRules);
                $data['tax_calculation'] ?? $data['tax_calculation'] = 0;
                $data['tax_included'] ?? $data['tax_included'] = 0;
            isset($data['tax_fix_percentage']) ? $data['tax_fix_percentage'] = 'percentage' : $data['tax_fix_percentage'] = 'fix';
            $tax->update($data);
            return response()->json(['success', 'You have updated the location']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        $tax->forceDelete();
        //        $tax->delete();
        return response()->json(['success' => 'The table is trashed!']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function restore(Tax $tax)
    {
        $tax->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Tax $tax)
    {
        $tax->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }
}
