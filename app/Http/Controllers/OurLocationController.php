<?php

namespace App\Http\Controllers;

use App\Models\EKiosk;
use App\Models\Location;
use App\Models\Tax;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class OurLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ourLocations = Location::get();
        return view('settings/ourLocations/ourLocation-index',  compact('ourLocations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings/ourLocations/ourLocation-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(Location::class)],
                'location' => 'required'
            ]);
            $data = $request->all();
            if (isset($data['kitchen'])) {
                $data['kitchen'] = true;
                $data['auto_print'] = isset($data['auto_print']);
            }else{
                $data['kitchen'] = false;
                $data['auto_print'] = false;
            }
            if (!(isset($data['delivery']) || isset($data['take_away']) || isset($data['dine_in']))){
                return response()->json(['errors' => __("You cannot create a tax if you have not selected the type, (dine in, takeout or delivery)")], 422);
            }
            $data['integrated_payments'] = isset($data['integrated_payments']) ?? 0;
            $data['manual_payments'] = isset($data['manual_payments']) ?? 0;
            Location::create($data);
            DB::commit();
            return response()->json(['success', 'You have added the location']);
        } catch (ValidationException $e) {
            DB::rollBack();
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
    public function edit(Location $ourLocation)
    {
        return view('settings/ourLocations/ourLocation-edit', compact('ourLocation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $ourLocation)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => ['required', Rule::unique('locations')->ignore($ourLocation->id)],
                'location' => 'required',
            ]);
            $data = $request->all();
            $data['pos'] = isset($data['pos']) ?? 0;
            $data['e_kiosk'] = isset($data['e_kiosk']) ?? 0;
            $data['dine_in'] = isset($data['dine_in']) ?? 0;
            $data['has_tables'] = isset($data['has_tables']) ?? 0;
            $data['has_locators'] = isset($data['has_locators']) ?? 0;
            $data['take_away'] = isset($data['take_away']) ?? 0;
            $data['delivery'] = isset($data['delivery']) ?? 0;
            $data['integrated_payments'] = isset($data['integrated_payments']) ?? 0;
            $data['manual_payments'] = isset($data['manual_payments']) ?? 0;
            if (isset($data['kitchen'])) {
                $data['kitchen'] = true;
                $data['auto_print'] = isset($data['auto_print']);
            }else{
                $data['kitchen'] = false;
                $data['auto_print'] = false;
            }
            $ourLocation->update($data);
            if (!$data['e_kiosk']){
                $ekioskIds = DB::table('e_kiosks')
                    ->where('location_id', $ourLocation->id)
                    ->pluck('id')
                    ->toArray();
                DB::table('personal_access_tokens')
                    ->whereIn('tokenable_id', $ekioskIds)
                    ->update(['expires_at' => now()]);
            }

            if ($this->isAdmin()){
                $id = session()->get('localization_for_changes_data')?->id;
                session()->put('localization_for_changes_data', Location::where('id', $id)->first());
            }
            DB::commit();
            return response()->json(['success', 'You have updated the location']);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $ourLocation)
    {
        if ($ourLocation->isPrime()) {
            return response()->json(['error' => "You can't delete ".$ourLocation->name." location because is main location, you can edit it!"], 422);
        }
//        $ourLocation->forceDelete();
        $ourLocation->delete();
        return response()->json(['success' => 'The location is trashed!']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function restore(Location $ourLocation)
    {
        $ourLocation->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Location $ourLocation)
    {
        $ourLocation->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }

    public function checkTaxesForLocation(Request $request){
        $tax = Tax::where('type', $request->query->get('type'))->first();
        if ($tax){
            return response()->json([
                'status' => 0,
                'message' => "",
            ]);
        }
        return response()->json([
            'status' => 1,
            'message' => __("You cannot activate this service form as you have not created a tax for this type, please create a tax for this service form!"),
            "uri" => route('tax.create')
        ]);
    }
}
