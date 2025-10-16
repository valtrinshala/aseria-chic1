<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::get();
        return view('settings/units/unit-index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'settings/units/unit-create' : 'pop-up-locations');
        }
        return view('settings/units/unit-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();;
        try {
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(Unit::class)],
                'suffix' => 'required',
            ]);
            Unit::create($data);
            return response()->json(['success', 'You have added the units']);
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
    public function edit(Unit $unit)
    {
        return view('settings/units/unit-edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $data = $request->all();
        try {
            $request->validate([
                'name' => ['required',
                    Rule::unique('units')->where(function ($query) use ($request, $unit) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($unit->id),
                ],
                'suffix' => 'required',
            ]);
            $unit->update($data);
            return response()->json(['success', 'You have updated the unit']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->forceDelete();
        //        $tax->delete();
        return response()->json(['success' => 'The unit is trashed!']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function restore(Unit $unit)
    {
        $unit->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Unit $unit)
    {
        $unit->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }
}
