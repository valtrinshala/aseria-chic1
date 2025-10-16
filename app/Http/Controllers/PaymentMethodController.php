<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('created_at', 'asc')->get();
        return view('payment-methods/paymentMethod-index', compact('paymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, PaymentMethod $paymentMethod)
    {
        $colors = [
            ['#f44336', '#e81e63', '#9c27b0'],
            ['#673ab7', '#3f51b5', '#2196f3'],
            ['#03a9f4', '#00bcd4', '#009688'],
            ['#4caf50', '#8bc34a', '#cddc39'],
            ['#ffeb3b', '#ffc107', '#ff9800'],
            ['#ff5722', '#795548', '#9e9e9e'],
        ];

        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'payment-methods/paymentMethod-create' : 'pop-up-locations', compact('colors'));
        }
        return view('payment-methods/paymentMethod-create', compact('colors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(PaymentMethod::class)],
                'image' => 'required|extensions:jpg,jpeg,png,svg,webp'
            ]);
            PaymentMethod::create([
                'id' => Uuid::uuid4()->toString(),
                'name' => $request['name'],
                'status' => $request['status'] ?? false,
                'image' => $this->itemImageValidated($request),
                'color' => $request['color'] ?? null
            ]);
            return response()->json(['success', 'You have added the payment method']);
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
    public function edit(PaymentMethod $paymentMethod)
    {
        $colors = [
            ['#f44336', '#e81e63', '#9c27b0'],
            ['#673ab7', '#3f51b5', '#2196f3'],
            ['#03a9f4', '#00bcd4', '#009688'],
            ['#4caf50', '#8bc34a', '#cddc39'],
            ['#ffeb3b', '#ffc107', '#ff9800'],
            ['#ff5722', '#795548', '#9e9e9e'],
        ];
        return view('payment-methods/paymentMethod-edit', compact('paymentMethod', 'colors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->all();
        try {
            $request->validate([
                'name' => ['required',
                    Rule::unique('payment_methods')->where(function ($query) use ($request, $paymentMethod) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($paymentMethod->id),
                ],
                'image' => 'extensions:jpg,jpeg,png,svg,webp'
            ]);
            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            if ($request->image) {
                $data['image'] = $this->itemImageValidated($request);
                if (!empty($paymentMethod->image)) {
                    Storage::disk('public')->delete($paymentMethod->image);
                }
            }
            $paymentMethod->update($data);
            return response()->json(['success', 'You have updated the payment method']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->isPrime()) {
            return response()->json(['error' => "You cannot delete this payment method because it is predefined"], 422);
        }
        $imagePath = $paymentMethod->image;
        $paymentMethod->forceDelete();
        //        $paymentMethod->delete();
        if (!empty($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        return response()->json(['success' => 'The payment method is trashed!']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function restore(PaymentMethod $paymentMethod)
    {
        $paymentMethod->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(PaymentMethod $paymentMethod)
    {
        $paymentMethod->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }

    protected function itemImageValidated($request): string
    {
        $data = '';
        if ($request->file('image')) {
            $data = $request->file('image')
                ->store('paymentMethods', 'public');
        }
        return $data;
    }
}
