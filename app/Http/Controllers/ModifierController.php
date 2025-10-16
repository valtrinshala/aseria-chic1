<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use App\Models\Ingredient;
use App\Models\Location;
use App\Models\Modifier;
use App\Models\ModifierTranslation;
use App\Models\Unit;
use App\Rules\UniqueNameForLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class ModifierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modifiers = Modifier::get();
        return view('modifiers/modifiers-index', compact('modifiers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ingredients = Ingredient::get();
        $categories = FoodCategory::where('status', true)
            ->where('id', '!=', config('constants.api.sauceId'))
            ->where('id', '!=', config('constants.api.sideId'))
            ->where('id', '!=', config('constants.api.dealId'))->get();
        $units = Unit::get();
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'modifiers/modifier-create' : 'pop-up-locations', compact('ingredients', 'categories', 'units'));
        }
        return view('modifiers/modifier-create', compact('ingredients', 'categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => ['required', new UniqueNameForLocation(Modifier::class)],
                'price' => 'required',
                'cost' => 'required',
//                'image' => 'required|extensions:jpg,jpeg,png,svg,webp'
            ]);
            $requests = $request->all();
            $id = Uuid::uuid4()->toString();
            $data = [
                'id' => $id,
                'sku' => (int)round(microtime(true) * 1000),
                'title' => $requests['title'],
                'price' => $requests['price'],
                'cost' => $requests['cost'],
                'status' => $requests['status'],
                'image' => $this->itemImageValidated($request),
                'description' => $requests['description'] ?? null,
            ];
            $modifier = Modifier::create($data);
            ModifierTranslation::create([
                'language_id' => config('constants.language.languageId'),
                'modifier_id' => $id,
                'title' => $requests['title'],
                'description' => $requests['description'],
            ]);
            $modifier->id = $id;
            $this->syncIngredient($request, $modifier);
            $this->syncCategory($request, $modifier);
            return response()->json(['success', 'You have added the ingredient']);
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
    public function edit(Modifier $modifier)
    {
        $categories = FoodCategory::where('status', true)
            ->where('id', '!=', config('constants.api.sauceId'))
            ->where('id', '!=', config('constants.api.sideId'))
            ->where('id', '!=', config('constants.api.dealId'))->get();
        $ingredients = Ingredient::all();
        $units = Unit::get();
        return view('modifiers/modifier-edit', compact('modifier', 'ingredients', 'categories', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Modifier $modifier)
    {
        try {
            $request->validate([
                'title' => ['required',
                    Rule::unique('modifiers')->where(function ($query) use ($request, $modifier) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($modifier->id),
                ],
                'price' => 'required',
                'cost' => 'required',
//                'image' => 'extensions:jpg,jpeg,png,svg,webp'
            ]);
            $requests = $request->all();
            $data = [
                'title' => $requests['title'],
                'price' => $requests['price'],
                'cost' => $requests['cost'],
                'status' => $requests['status'],
                'description' => $requests['description'] ?? null,
            ];
            if ($request->image) {
                $data['image'] = $this->itemImageValidated($request);
                if (!empty($modifier->image)) {
                    Storage::disk('public')->delete($modifier->image);
                }
            }
            $mainLanguageId = config('constants.language.languageId');
            if (!$requests['languageId'] || $requests['languageId'] == $mainLanguageId) {
                ModifierTranslation::updateOrCreate([
                    'language_id' => $mainLanguageId,
                    'modifier_id' => $modifier->id,
                ],
                [
                    'title' => $requests['title'],
                    'description' => $requests['description']
                ]);
            } else {
                ModifierTranslation::updateOrCreate([
                    'language_id' => $requests['languageId'],
                    'modifier_id' => $modifier->id,
                ],
                    [
                        'title' => $requests['title'],
                        'description' => $requests['description']
                    ]);
                unset($data['title'], $data['description']);
            }
            $modifier->update($data);
            $this->syncIngredient($request, $modifier);
            $this->syncCategory($request, $modifier);
            return response()->json(['success', 'The modifier is updated']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Modifier $modifier)
    {
        $modifier->ingredients()->sync([]);
//        $modifier->delete();
        $modifier->forceDelete();
        return response()->json(['success' => 'The modifier is trashed!']);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
//        Modifier::whereIn('id', $request->ids)->delete();
        Modifier::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(Modifier $modifier)
    {
        $modifier->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Modifier $modifier)
    {
        $modifier->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }

    protected function itemImageValidated($request): string
    {
        $data = '';
        if ($request->file('image')) {
            $data = $request->file('image')
                ->store('products', 'public');
        }
        return $data;
    }


    protected function syncIngredient($request, $modifier)
    {
        if ($request['ingredients'] && count($request['ingredients']) != 0) {
            $modifier->ingredients()->sync([]);
            foreach ($request['ingredients'] as $ingredientId => $value) {
                $modifier->ingredients()->attach($ingredientId, [
                    'quantity' => $value['qty'],
                    'unit' => $value['unit'],
                    'cost' => $value['cost'],
                    'price' => $value['price'],
                    'cost_per_unit' => $value['cost_per_unit'],
                    'price_per_unit' => $value['price_per_unit']
                ]);
            }
        }
    }

    protected function syncCategory($request, $modifier)
    {
        if ($request['categories'] && count($request['categories']) != 0) {
            $modifier->category()->sync([]);
            foreach ($request['categories'] as $categoryId) {
                $modifier->category()->attach($categoryId);
            }
        }
    }
}
