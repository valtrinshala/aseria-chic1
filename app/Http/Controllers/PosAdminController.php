<?php

namespace App\Http\Controllers;

use App\Models\AndroidModels\Pos;
use App\Models\AndroidModels\PosIncomingRequest;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use function Psy\debug;

class PosAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $poses = Pos::get();
        return view('pos/admin/pos-index', compact('poses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $posIncomingRequest = PosIncomingRequest::find($request->posIncomingRequestId);
        if($this->isAdmin()){
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'pos/admin/pos-create' : 'pop-up-locations', compact('posIncomingRequest'));
        }
        return view('pos/admin/pos-create', compact('posIncomingRequest'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(Pos::class)],
                'pos_id' => 'required|unique:pos',
                'location_id' => 'required',
                'pin_for_settings' => 'required'
            ]);
            $data = $request->all();
            $checkPos = DB::table('pos')->where(['name' => $data['name'], 'location_id' => $data['location_id']])->exists();
            if ($checkPos){
                return response()->json(['errors' => ['name' => [__('The name must be unique within the specified location.')]]], 422);
            }
            $data['id'] = Uuid::uuid4()->toString();
            $data['user_id'] = auth()->id();
            unset($data['requestId']);
            DB::table('pos')->insert($data);
            $posIncomingRequestId = $request->requestId;
            if ($posIncomingRequestId){
                $incomingRequest = PosIncomingRequest::find($posIncomingRequestId);
                $incomingRequest->delete();
            }
            return response()->json(['success', 'Pos is created']);
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
    public function edit(Pos $devices_po)
    {
        $pos = $devices_po;
        return view('pos/admin/pos-edit', compact( 'pos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pos $devices_po)
    {
        $pos = $devices_po;
        try {
            $request->validate([
                'name' => ['required',
                    Rule::unique('pos')->where(function ($query) use ($request, $pos) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($pos->id),
                ],
                'pin_for_settings' => 'required'
            ]);
            $data = $request->all();

            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            unset($data['pos_id'], $data['_method']);
            DB::table('pos')->where('id', $pos->id)->update($data);
            return response()->json(['success', 'Updated pos']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pos $devices_po)
    {
        $devices_po->forceDelete();
        return response()->json(['success', "Pos deleted successfully"]);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
//        Pos::whereIn('id', $request->ids)->delete();
        Pos::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(Pos $devices_po)
    {
        $devices_po->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Pos $devices_po)
    {
        $devices_po->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }


    public function posIncomingRequests()
    {
        try {
            $incomingRequests = PosIncomingRequest::get();
            return view('pos/admin/pos-incoming-requests/incoming-request-index', compact('incomingRequests'));
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyIncomingRequests(PosIncomingRequest $incomingRequest)
    {
        //        $incomingRequest->delete();
        $incomingRequest->forceDelete();
        return response()->json(['success' => 'The record is trashed']);
    }
}
