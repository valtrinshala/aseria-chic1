<?php

namespace App\Http\Controllers;

use App\Models\AndroidModels\IncomingRequest;
use App\Models\EKiosk;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class EKioskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eKiosks = EKiosk::get();
        return view('e-kiosks/eKiosk-index', compact('eKiosks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $incomingRequest = IncomingRequest::find($request->incomingRequestId);
        if($this->isAdmin()){
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'e-kiosks/eKiosk-create' : 'pop-up-locations', compact('incomingRequest'));
        }
        return view('e-kiosks/eKiosk-create', compact('incomingRequest'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(EKiosk::class)],
                'e_kiosk_id' => 'required|unique:e_kiosks',
                'location_id' => 'required',
                'pin_for_settings' => 'required'
            ]);
            $data = $request->all();
            $checkEkiosk = DB::table('e_kiosks')->where([['name', $data['name']],['location_id', $data['location_id']]])->exists();
            if ($checkEkiosk){
                return response()->json(['errors' => ['name' => [__('The name must be unique within the specified location.')]]], 422);
            }
            $data['id'] = Uuid::uuid4()->toString();
            $data['user_id'] = auth()->id();
            unset($data['requestId']);
            DB::table('e_kiosks')->insert($data);
            $incomingRequestId = $request->requestId;
            if ($incomingRequestId){
                $incomingRequest = IncomingRequest::find($incomingRequestId);
                $incomingRequest->delete();
            }
            return response()->json(['success', 'E Kiosk Created']);
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
    public function edit(EKiosk $eKiosk)
    {
        return view('e-kiosks/eKiosk-edit', compact( 'eKiosk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EKiosk $eKiosk)
    {
        try {
            $request->validate([
                'name' => ['required',
                    Rule::unique('e_kiosks')->where(function ($query) use ($request, $eKiosk) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($eKiosk->id),
                ],
                'pin_for_settings' => 'required'
            ]);
            $data = $request->all();

            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            unset($data['e_kiosk_id'], $data['_method']);
            DB::table('e_kiosks')->where('id', $eKiosk->id)->update($data);
            return response()->json(['success', 'Updated E kiosk ']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EKiosk $eKiosk)
    {
        //$eKiosk->delete();
        $eKiosk->forceDelete();
        return response()->json(['success', "E kiosk deleted successfully"]);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
//        EKiosk::whereIn('id', $request->ids)->delete();
        EKiosk::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function restore(EKiosk $eKiosk)
    {
        $eKiosk->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(EKiosk $eKiosk)
    {
        $eKiosk->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }
}
