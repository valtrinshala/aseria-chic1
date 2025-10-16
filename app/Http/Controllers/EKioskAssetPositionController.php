<?php

namespace App\Http\Controllers;

use App\Models\PositionAsset;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EKioskAssetPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eKioskPositions = PositionAsset::with('eKioskAsset')->get();
        return view('e-kiosks/e-kiosk-asset-positions/position-index', compact('eKioskPositions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'e-kiosks/e-kiosk-asset-positions/position-create' : 'pop-up-locations');
        }
        return view('e-kiosks/e-kiosk-asset-positions/position-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(PositionAsset::class)],
                'asset_key' => ['required', new UniqueNameForLocation(PositionAsset::class)],
            ]);
            $data = $request->all();
            PositionAsset::create($data);
            return response()->json(['success', 'Asset Created']);
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
    public function edit(PositionAsset $eKioskAssetPosition)
    {
        return view('e-kiosks/e-kiosk-asset-positions/position-edit', compact('eKioskAssetPosition'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PositionAsset $eKioskAssetPosition)
    {
        try {
            $request->validate([
                'name' => ['required',
                    Rule::unique('position_assets')->where(function ($query) use ($request, $eKioskAssetPosition) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($eKioskAssetPosition->id),
                ],
                'asset_key' => ['required',
                    Rule::unique('position_assets')->where(function ($query) use ($request, $eKioskAssetPosition) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($eKioskAssetPosition->id),
                ],
            ]);
            $data = $request->all();
            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            $eKioskAssetPosition->update($data);
            return response()->json(['success', 'Updated E kiosk ']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PositionAsset $eKioskAssetPosition)
    {
        //$positionAsset->delete();
        $eKioskAssetPosition->forceDelete();
        return response()->json(['success', "E kiosk deleted successfully"]);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        //        PositionAsset::whereIn('id', $request->ids)->delete();
        PositionAsset::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function restore(PositionAsset $eKioskAssetPosition)
    {
        $eKioskAssetPosition->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(PositionAsset $eKioskAssetPosition)
    {
        $eKioskAssetPosition->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }

}
