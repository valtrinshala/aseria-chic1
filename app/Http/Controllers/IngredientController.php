<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientTranslation;
use App\Models\Location;
use App\Models\Unit;
use App\Rules\UniqueNameForLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ingredients = Ingredient::get();
        return view('ingredients/ingredient-index', compact('ingredients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = Unit::get();

        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'ingredients/ingredient-create' : 'pop-up-locations', compact('units'));
        }
        return view('ingredients/ingredient-create', compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(Ingredient::class)],
                'price' => 'required',
                'cost' => 'required',
                'unit' => 'required',
                'quantity' => 'required',
                'alert_quantity' => 'required',
            ]);
            $requests = $request->all();
            $id = Uuid::uuid4()->toString();
            $data = [
                'id' => $id,
                'name' => $requests['name'],
                'price' => $requests['price'],
                'cost' => $requests['cost'],
                'unit' => $requests['unit'],
                'quantity' => $requests['quantity'],
                'alert_quantity' => $requests['alert_quantity'],
            ];
            Ingredient::create($data);
            IngredientTranslation::create([
                'language_id' => config('constants.language.languageId'),
                'ingredient_id' => $id,
                'name' => $data['name'],
            ]);
            DB::commit();
            return response()->json(['success', 'You have added the ingredient']);
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
    public function edit(Ingredient $ingredient)
    {
        $units = Unit::get();
        return view('ingredients/ingredient-edit', compact('ingredient', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => ['required',
                    Rule::unique('ingredients')->where(function ($query) use ($request, $ingredient) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($ingredient->id),
                ],
                'price' => 'required',
                'cost' => 'required',
                'unit' => 'required',
                'quantity' => 'required',
                'alert_quantity' => 'required',
            ]);
            $requests = $request->all();
            $name = $requests['name'];
            $languageId = $requests['languageId'];
            $data = [
                'name' => $name,
                'price' => $requests['price'],
                'cost' => $requests['cost'],
                'unit' => $requests['unit'],
                'quantity' => $requests['quantity'],
                'alert_quantity' => $requests['alert_quantity'],
            ];
            $mainLanguageId = config('constants.language.languageId');
            if (!$languageId || $languageId == $mainLanguageId){
                IngredientTranslation::updateOrCreate([
                    'language_id' => $mainLanguageId,
                    'ingredient_id' => $ingredient->id,
                ], [
                    'name' => $name,
                ]);
            }else{
                IngredientTranslation::updateOrCreate([
                    'language_id' => $languageId,
                    'ingredient_id' => $ingredient->id,
                ], [
                    'name' => $name,
                ]);
                unset($data['name']);
            }
            $ingredient->update($data);
            DB::commit();
            return response()->json(['success', 'The ingredient is updated']);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->forceDelete();
        //        $ingredient->delete();
        return response()->json(['success' => 'The ingredient is trashed!']);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        //        Ingredient::whereIn('id', $request->ids)->delete();
        Ingredient::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(Ingredient $ingredient)
    {
        $ingredient->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Ingredient $ingredient)
    {
        $ingredient->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }
}
