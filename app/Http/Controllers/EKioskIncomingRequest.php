<?php

namespace App\Http\Controllers;

use App\Models\AndroidModels\IncomingRequest;
use Illuminate\Http\Request;

class EKioskIncomingRequest extends Controller
{

    public function incomingRequests()
    {
        $incomingRequests = IncomingRequest::get();
        return view('e-kiosks/e-kiosk-incoming-requests/incoming-request-index', compact('incomingRequests'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IncomingRequest $incomingRequest)
    {
        //        $incomingRequest->delete();
        $incomingRequest->forceDelete();
        return response()->json(['success' => 'The record is trashed']);
    }
}
