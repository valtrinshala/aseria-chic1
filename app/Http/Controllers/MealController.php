<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use App\Models\FoodItem;
use App\Models\Ingredient;
use App\Models\Location;
use App\Models\Meal;
use App\Models\MealTranslation;
use App\Models\Tax;
use App\Rules\UniqueNameForLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $meals = Meal::get();
        return view('meals/meal-index', compact('meals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $foodItems = FoodItem::where('status', true)->get();
        $category = FoodCategory::find(config('constants.api.dealId'));
        $taxes = Tax::whereNotIn('type', ['dine_in', 'take_away', 'delivery'])->orWhereNull('type')->get();
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'meals/meal-create' : 'pop-up-locations', compact('foodItems', 'category', 'taxes'));
        }
        return view('meals/meal-create', compact('foodItems', 'category', 'taxes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(Meal::class)],
                'price' => 'required',
                'cost' => 'required',
                'image' => 'required|extensions:jpg,jpeg,png,svg,webp'
            ]);
            $requests = $request->all();
            $id = Uuid::uuid4()->toString();
            $data = [
                'id' => $id,
                'tax_id' => $requests['tax_id'],
                'food_category_id' => $requests['categories'],
                'sku' => (int)round(microtime(true) * 1000),
                'name' => $requests['name'],
                'price' => $requests['price'],
                'cost' => $requests['cost'],
                'status' => $requests['status'],
                'image' => $this->itemImageValidated($request),
                'description' => $requests['description'],
            ];
            $meal = Meal::create($data);
            MealTranslation::create([
                'language_id' => config('constants.language.languageId'),
                'meal_id' => $id,
                'name' => $requests['name'],
                'description' => $requests['description'],
            ]);
            $meal->id = $id;
            $this->syncfoodItem($request, $meal);

            return response()->json(['success' => 'You have added meal']);
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
    public function edit(Meal $meal)
    {
        $foodItems = FoodItem::where('status', true)->get();
        $category = FoodCategory::find(config('constants.api.dealId'));
        $taxes = Tax::whereNotIn('type', ['dine_in', 'take_away', 'delivery'])->orWhereNull('type')->get();
        return view('meals/meal-edit', compact('meal', 'foodItems', 'category', 'taxes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meal $meal)
    {

        try {
            $request->validate([
                'name' => ['required',
                    Rule::unique('meals')->where(function ($query) use ($request, $meal) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($meal->id),
                ],
                'price' => 'required',
                'cost' => 'required',
                'image' => 'extensions:jpg,jpeg,png,svg,webp'
            ]);
            $requests = $request->all();
            $hasFoodItemsFalse = (bool)$meal->foodItems->firstWhere('status', false);
            if ($hasFoodItemsFalse && $requests['status'] == '1'){
                $mealNames = $meal->foodItems->pluck('name')->toArray();
                return response()->json(['errors' => ['title' => [__('You cannot activate this meal because some products that make up this meal have been deactivated'). implode(', ', $mealNames) ]]], 422);
            }


            $data = [
                'food_category_id' => $requests['categories'],
                'tax_id' => $requests['tax_id'] ?? null,
                'name' => $requests['name'],
                'price' => $requests['price'],
                'cost' => $requests['cost'],
                'status' => $requests['status'],
                'description' => $requests['description'],
            ];
            if ($request->image) {
                $data['image'] = $this->itemImageValidated($request);
                if (!empty($meal->image)) {
                    Storage::disk('public')->delete($meal->image);
                }
            }
            $mainLanguageId = config('constants.language.languageId');
            if (!$requests['languageId'] || $requests['languageId'] == $mainLanguageId){
                MealTranslation::updateOrCreate([
                    'language_id' => $mainLanguageId,
                    'meal_id' => $meal->id,
                ], [
                    'name' => $requests['name'],
                    'description' => $requests['description'],
                ]);
            }else{
                MealTranslation::updateOrCreate([
                    'language_id' => $requests['languageId'],
                    'meal_id' => $meal->id,
                ], [
                    'name' => $requests['name'],
                    'description' => $requests['description'],
                ]);
                unset($data['name'], $data['description']);
            }
            $meal->update($data);
            $this->syncfoodItem($request, $meal);
            return response()->json(['success', 'The product is updated']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meal $meal)
    {
        //        $meal->delete();
        $meal->forceDelete();
        return response()->json(['success' => 'The record is trashed']);
    }


    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        //        Meal::whereIn('id', $request->ids)->delete();
        Meal::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(Meal $meal)
    {
        $meal->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Meal $meal)
    {
        $meal->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }

    protected function itemImageValidated($request): string
    {
        $data = '';
        if ($request->file('image')) {
            $data = $request->file('image')
                ->store('meal/categories', 'public');
        }
        return $data;
    }

    protected function syncfoodItem($request, $meal)
    {
        if ($request['foodItems'] && count($request['foodItems'])) {
            $meal->foodItems()->sync([]);
            foreach ($request['foodItems'] as $foodItemId => $qty) {
                $meal->foodItems()->attach($foodItemId, ['quantity' => $qty]);
            }
        }
    }
}
