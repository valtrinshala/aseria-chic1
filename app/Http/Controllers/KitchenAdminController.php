<?php

namespace App\Http\Controllers;

use App\Models\AndroidModels\Kitchen;
use App\Models\AndroidModels\KitchenIncomingRequest;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use function Psy\debug;

class KitchenAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kitchens = Kitchen::get();
        return view('kitchen/admin/kitchen-index', compact('kitchens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $kitchenIncomingRequest = KitchenIncomingRequest::find($request->kitchenIncomingRequestId);
        if($this->isAdmin()){
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'kitchen/admin/kitchen-create' : 'pop-up-locations', compact('kitchenIncomingRequest'));
        }
        return view('kitchen/admin/kitchen-create', compact('kitchenIncomingRequest'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(Kitchen::class)],
                'kitchen_id' => 'required|unique:kitchens',
                'location_id' => 'required',
                'pin_for_settings' => 'required'
            ]);
            $data = $request->all();
            $checkKitchen = DB::table('kitchens')->where(['name' => $data['name'], 'location_id' => $data['location_id']])->exists();
            if ($checkKitchen){
                return response()->json(['errors' => ['name' => [__('The name must be unique within the specified location.')]]], 422);
            }
            $data['id'] = Uuid::uuid4()->toString();
            $data['user_id'] = auth()->id();
            unset($data['requestId']);
            DB::table('kitchens')->insert($data);
            $kitchenIncomingRequestId = $request->requestId;
            if ($kitchenIncomingRequestId){
                $incomingRequest = KitchenIncomingRequest::find($kitchenIncomingRequestId);
                $incomingRequest->delete();
            }
            return response()->json(['success', 'Kitchen is created']);
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
     * Show the form for editing the specified resource.
     */
    public function edit(Kitchen $device)
    {
        $kitchen = $device;
        return view('kitchen/admin/kitchen-edit', compact( 'kitchen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kitchen $device)
    {
        $kitchen = $device;
        try {
            $request->validate([
                'name' => ['required',
                    Rule::unique('kitchens')->where(function ($query) use ($request, $kitchen) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($kitchen->id),
                ],
                'pin_for_settings' => 'required'
            ]);
            $data = $request->all();

            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            unset($data['kitchen_id'], $data['_method']);
            DB::table('kitchens')->where('id', $kitchen->id)->update($data);
            return response()->json(['success', 'Updated kitchen']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kitchen $device)
    {
        $kitchen = $device;
        $kitchen->forceDelete();
        return response()->json(['success', "Kitchen deleted successfully"]);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
//        Kitchen::whereIn('id', $request->ids)->delete();
        Kitchen::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(Kitchen $device)
    {
        $kitchen = $device;
        $kitchen->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Kitchen $device)
    {
        $device->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }


    public function kitchenIncomingRequests()
    {
        try {
            $incomingRequests = KitchenIncomingRequest::get();
            return view('kitchen/admin/kitchen-incoming-requests/incoming-request-index', compact('incomingRequests'));
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyIncomingRequests(KitchenIncomingRequest $incomingRequest)
    {
        //        $incomingRequest->delete();
        $incomingRequest->forceDelete();
        return response()->json(['success' => 'The record is trashed']);
    }
}
