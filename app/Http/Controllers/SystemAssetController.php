<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\SystemAsset;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SystemAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $systemAssets = SystemAsset::get();
        return view('system-assets/systemAsset-index', compact('systemAssets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cashRegisters = CashRegister::get();
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'system-assets/systemAsset-create' : 'pop-up-locations', compact( 'cashRegisters'));
        }
        return view('system-assets/systemAsset-create', compact('cashRegisters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(SystemAsset::class)],
                'cash_register_id' => 'required',
                'image' => 'required|extensions:jpg,jpeg,png,svg,webp'
            ]);
            $data = $request->all();
            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            if ($request->file('image')) {
                $data['image'] = $this->itemImageValidated($request);
            }
            SystemAsset::create($data);
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
    public function edit(SystemAsset $systemAsset)
    {
        $cashRegisters = CashRegister::get();
        return view('system-assets/systemAsset-edit', compact('systemAsset', 'cashRegisters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemAsset $systemAsset)
    {
        try {
            $request->validate([
                'name' => ['required',
                    Rule::unique('system_assets')->where(function ($query) use ($request, $systemAsset) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($systemAsset->id),
                ],
                'cash_register_id' => 'required',
                'image' => 'extensions:jpg,jpeg,png,svg,webp'
            ]);
            $data = $request->all();
            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            if ($request->file('image')) {
                $data['image'] = $this->itemImageValidated($request);
                if (!empty($systemAsset->image)) {
                    Storage::disk('public')->delete($systemAsset->image);
                }
            }
            $systemAsset->update($data);
            return response()->json(['success', 'Updated E kiosk ']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemAsset $systemAsset)
    {
        $imagePath = $systemAsset->image;
        //$systemAsset->delete();
        if (!empty($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        $systemAsset->forceDelete();
        return response()->json(['success', "E kiosk deleted successfully"]);
    }


    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        $models = SystemAsset::whereIn('id', $request->ids);
        $imagePaths = $models->pluck('image');
        //        $models->delete();
        $models->forceDelete();
        $imagePaths->each(function ($imagePath) {
            if (!empty($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        });
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(SystemAsset $systemAsset)
    {
        $systemAsset->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(SystemAsset $systemAsset)
    {
        $systemAsset->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }
    protected function itemImageValidated($request): string
    {
        $data = '';
        if ($request->file('image')) {
            $data = $request->file('image')
                ->store('eKiosk/assets', 'public');
        }
        return $data;
    }

    public function getAssets(Request $request){

    }

}
