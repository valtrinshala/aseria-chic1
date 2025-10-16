<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::get();
        return view('people/customers/customer-index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'people/customers/customer-create' : 'pop-up-locations');
        }
        return view('people/customers/customer-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email'
            ]);
            $data = $request->all();
            Customer::create($data);
            return response()->json(['success', 'The customer is created']);
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
    public function edit(Customer $customer)
    {
        return view('people/customers/customer-edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email'
            ]);
            $data = $request->all();
            $customer->update($data);
            return response()->json(['success', 'the permissions are updated']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
//            $customer->delete();
            $customer->forceDelete();
            return response()->json(['success' => 'You deleted the customer']);
    }
    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
//        Customer::whereIn('id', $request->ids)->delete();
        Customer::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(Customer $customer)
    {
        $customer->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Customer $customer)
    {
        $customer->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }

}
