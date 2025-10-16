<?php

namespace App\Http\Controllers;

use App\Models\ServiceTable;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ServiceTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serviceTables = ServiceTable::get();
        return view('service-tables/serviceTable-index', compact('serviceTables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'service-tables/serviceTable-create' : 'pop-up-locations');
        }
        return view('service-tables/serviceTable-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => ['required', new UniqueNameForLocation(ServiceTable::class)],
            ]);
            ServiceTable::create(['title' => $request['title']]);
            return response()->json(['success', 'You have added the table']);
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
    public function edit(ServiceTable $serviceTable)
    {
        return view('service-tables/serviceTable-edit', compact('serviceTable'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceTable $serviceTable)
    {
        try {
            $request->validate([
                'title' => ['required',
                    Rule::unique('service_tables')->where(function ($query) use ($request, $serviceTable) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($serviceTable->id),
                ],
            ]);
            $serviceTable->update(['title' => $request['title']]);
            return response()->json(['success', 'You have updated the table']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceTable $serviceTable)
    {
        $serviceTable->forceDelete();
        //        $serviceTable->delete();
        return response()->json(['success' => 'The table is trashed!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(ServiceTable $serviceTable)
    {
        $serviceTable->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(ServiceTable $serviceTable)
    {
        $serviceTable->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }
}
