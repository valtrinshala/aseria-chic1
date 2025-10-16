<?php

namespace App\Http\Controllers;

use App\Models\QueueManagement;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QueueManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $queueManagement = QueueManagement::with('user')->get();
        return view('queue-management/queueManagement-index', compact('queueManagement'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'queue-management/queueManagement-create' : 'pop-up-locations');
        }
        return view('queue-management/queueManagement-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            $request->validate([
                'name' => ['required', new UniqueNameForLocation(QueueManagement::class)],
                'key' => 'required',
                'url' => 'required',
            ]);
            $data['user_id'] = auth()->id();
            QueueManagement::create($data);
            return response()->json(['success', 'Queue Created']);
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
    public function edit(QueueManagement $queueManagement)
    {
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'queue-management/queueManagement-edit' : 'pop-up-locations', compact('queueManagement'));
        }
        return view('queue-management/queueManagement-edit', compact('queueManagement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QueueManagement $queueManagement)
    {
        try {

            $request->validate([
                'name' => ['required',
                    Rule::unique('queue_management')->where(function ($query) use ($request, $queueManagement) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($queueManagement->id),
                ],
                'key' => 'required',
                'url' => 'required',
            ]);
            $data = $request->all();
            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            isset($data['authentication_code_status']) ? $data['authentication_code_status'] = 1 : $data['authentication_code_status'] = 0;
            $queueManagement->update($data);
            return response()->json(['success', 'Updated E kiosk ']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QueueManagement $queueManagement)
    {
        //$queueManagement->delete();
        $queueManagement->forceDelete();
        return response()->json(['success', "E kiosk deleted successfully"]);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        //        QueueManagement::whereIn('id', $request->ids)->delete();
        QueueManagement::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function restore(QueueManagement $queueManagement)
    {
        $queueManagement->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(QueueManagement $queueManagement)
    {
        $queueManagement->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }

    public function queueResults($locationName, $eKioskKey)
    {
        $queue = QueueManagement::where('key', $eKioskKey)->where('status', true)->first();
        if (!$queue) {
            abort(404);
        }
        if($queue->authentication_code_status && !session()->get('checkEKiosk')){
            return view('queue-management/queueManagement-login', compact('queue'));
        }
        return view('queue-management/queueManagement-results', compact('queue'));
    }


    public function checkPinForEKiosk(Request $request){
        $queue = QueueManagement::find($request->id);
        if ($queue->authentication_code == $request->pin){
            session()->put('checkEKiosk', true);
            session()->put('locale', $request->language);
            return back();
        }else{
            return back();
        }
    }
    public function queueResultsAjax($queueId, $locationId)
    {
        $sales = DB::table('sales')
            ->join('z_reports', 'sales.z_report_id', '=', 'z_reports.id')
            ->select('sales.order_number', 'sales.progress', 'sales.que_ready', 'sales.chef_id')
            ->whereNull('z_reports.end_z_report')
            ->whereNull('sales.completed_at')
            ->where('z_reports.location_id', $locationId)
            ->where('sales.is_cancelled', false)
            ->orderBy('sales.created_at', 'asc')
            ->get()
            ->toArray();
        $ready = array_filter($sales, function ($sale) {
            return $sale->que_ready == true;
        });

        $inProgress = array_filter($sales, function ($sale) {
            return $sale->que_ready == false && (($sale->chef_id == null && $sale->progress == 100) || $sale->chef_id != null);
        });
        return response()->json(['inProgress' => array_values($inProgress), 'ready' => array_values($ready)]);
    }
}
