<?php

namespace App\Http\Controllers;

use App\Models\EKiosk;
use App\Models\EKioskAsset;
use App\Models\PositionAsset;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use function PHPSTORM_META\type;

class EKioskAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eKioskAssets = EKioskAsset::get();
        return view('e-kiosks/e-kiosk-assets/asset-index', compact('eKioskAssets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $eKiosks = EKiosk::get();
        $positions = PositionAsset::with('eKioskAsset')->get();
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'e-kiosks/e-kiosk-assets/asset-create' : 'pop-up-locations', compact('positions', 'eKiosks'));
        }
        return view('e-kiosks/e-kiosk-assets/asset-create', compact('positions', 'eKiosks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'position_id' => 'required',
                'e_kiosk_id' => 'required',
                'image' => 'required|extensions:jpg,jpeg,png,svg,webp,mp4,avi,mov,flv,mkv'
            ]);
            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            $data = $request->all();
            $validateUniqueEKioskPosition = EKioskAsset::where(['position_id' => $data['position_id'], 'e_kiosk_id' => $data['e_kiosk_id']])->first();
            if ($validateUniqueEKioskPosition){
                return response()->json(['errors' => ['position_unique' => [__('The position must be unique within the specified eKiosk and location.')]]], 422);
            }
            if ($request->file('image')) {
                $data['image'] = $this->itemImageValidated($request);
                $type = null;
                if (str_contains($request->file('image')->getMimeType(), 'image')) {
                    $type = 'image';
                } elseif (str_contains($request->file('image')->getMimeType(), 'video')) {
                    $type = 'video';
                }
                $data['type'] = $type;
            }
            EKioskAsset::create($data);
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
    public function edit(EKioskAsset $eKioskAsset)
    {
        $eKiosks = EKiosk::get();
        $positions = PositionAsset::get();
        return view('e-kiosks/e-kiosk-assets/asset-edit', compact('eKioskAsset', 'positions', 'eKiosks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EKioskAsset $eKioskAsset)
    {
        try {
            $request->validate([
                'name' => 'required',
                'position_id' => 'required',
                'e_kiosk_id' => 'required',
                'image' => 'extensions:jpg,jpeg,png,svg,webp,mp4,avi,mov,flv,mkv'
            ]);
            $data = $request->all();
            $validateUniqueEKioskPosition = EKioskAsset::where(['position_id' => $data['position_id'], 'e_kiosk_id' => $data['e_kiosk_id']])
                ->where('id', '!=', $eKioskAsset->id)
                ->first();
            if ($validateUniqueEKioskPosition){
                return response()->json(['errors' => ['position_unique' => [__('The position must be unique within the specified eKiosk and location.')]]], 422);
            }
            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            if ($request->file('image')) {
                $data['image'] = $this->itemImageValidated($request);
                if (!empty($eKioskAsset->image)) {
                    Storage::disk('public')->delete($eKioskAsset->image);
                }
                $type = null;
                if (str_contains($request->file('image')->getMimeType(), 'image')) {
                    $type = 'image';
                } elseif (str_contains($request->file('image')->getMimeType(), 'video')) {
                    $type = 'video';
                }
                $data['type'] = $type;
            }
            $eKioskAsset->update($data);
            return response()->json(['success', 'Updated E kiosk ']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EKioskAsset $eKioskAsset)
    {
        $imagePath = $eKioskAsset->image;
        //$eKioskAsset->delete();
        if (!empty($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        $eKioskAsset->forceDelete();
        return response()->json(['success', "E kiosk deleted successfully"]);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        $models = EKioskAsset::whereIn('id', $request->ids);
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
    public function restore(EKioskAsset $eKioskAsset)
    {
        $eKioskAsset->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(EKioskAsset $eKioskAsset)
    {
        $eKioskAsset->forceDelete();
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
}
