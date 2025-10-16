<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use App\Models\FoodItem;
use App\Models\FoodItemTranslation;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\Tax;
use App\Models\Unit;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Illuminate\Validation\ValidationException;
use function PHPUnit\Framework\isEmpty;

class FoodItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $foodItems = FoodItem::get();
        return view('food-items/foodItem-index', compact('foodItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $variants = [
            'large' => [
                'inputName' => 'large',
                'label' => __('Large'),
            ],
            'medium' => [
                'inputName' => 'medium',
                'label' => __('Medium'),
            ],
            'small' => [
                'inputName' => 'small',
                'label' => __('Small'),
            ],
        ];
        $ingredients = Ingredient::all();
        $categories = FoodCategory::where('status', true)->where('id', '!=', config('constants.api.dealId'))->get();
        $taxes = Tax::whereNotIn('type', ['dine_in', 'take_away', 'delivery'])->orWhereNull('type')->get();
        $units = Unit::get();
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'food-items/foodItem-create' : 'pop-up-locations', compact('ingredients', 'categories', 'variants', 'units', 'taxes'));
        }
        return view('food-items/foodItem-create', compact('ingredients', 'categories', 'variants', 'units', 'taxes'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(FoodItem::class)],
                'price' => 'required',
                'cost' => 'required',
                'image' => 'required|extensions:jpg,jpeg,png,svg,webp',
                'second_image' => 'required|extensions:jpg,jpeg,png,svg,webp'
            ]);
            $requests = $request->all();
            $id = Uuid::uuid4()->toString();
            $size = [
                'small' => null,
                'medium' => null,
                'large' => null,
            ];

            $small = isset($requests['small']['selected']);
            $medium = isset($requests['medium']['selected']);
            $large = isset($requests['large']['selected']);
            if (isset($requests['size-status'])) {
                if (($small && $medium) || ($small && $large) || ($medium && $large)) {
                    $size['small'] = $small ? 0 : null;
                    $size['medium'] = $medium ? ($small ? ($requests['medium']['price'] * 1 ?? 0) : 0) : null;
                    $size['large'] = $large ? $requests['large']['price'] * 1 : null;
                }
            }
            $images = $this->itemImageValidated($request);
            $data = [
                'id' => $id,
                'tax_id' => $requests['tax_id'],
                'food_category_id' => $requests['categories'],
                'sku' => (int)round(microtime(true) * 1000),
                'name' => $requests['name'],
                'price' => $requests['price'],
                'cost' => $requests['cost'],
                'status' => $requests['status'],
                'image' => $images['image'],
                'second_image' => $images['secondImage'],
                'description' => $requests['description'],
                'size' => $size,
                'price_change' => isset($requests['price_change']),
            ];
            $foodItem = FoodItem::create($data);
            $foodItem->id = $id;
            FoodItemTranslation::create([
                'language_id' => config('constants.language.languageId'),
                'food_item_id' => $id,
                'name' => $requests['name'],
                'description' => $requests['description'],
            ]);
            $this->syncIngredient($request, $foodItem);
            DB::commit();
            return response()->json(['success' => 'You have added product']);
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
    public function edit(FoodItem $foodItem)
    {
        $variants = [
            'large' => [
                'inputName' => 'large',
                'label' => __('Large'),
            ],
            'medium' => [
                'inputName' => 'medium',
                'label' => __('Medium'),
            ],
            'small' => [
                'inputName' => 'small',
                'label' => __('Small'),
            ],
        ];
        $hasSize = false;
        foreach ($foodItem->size as $eachSize) {
            if (is_numeric($eachSize)) {
                $hasSize = true;
                break;
            }
        }
        $ingredients = Ingredient::get();
        $categories = FoodCategory::where('status', true)->where('id', '!=', config('constants.api.dealId'))->get();
        $taxes = Tax::whereNotIn('type', ['dine_in', 'take_away', 'delivery'])->orWhereNull('type')->get();
        $units = Unit::get();
        return view('food-items/foodItem-edit', compact('foodItem', 'ingredients', 'categories', 'variants', 'hasSize', 'units', 'taxes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FoodItem $foodItem)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => [
                    'required',
                    Rule::unique('cash_registers')->where(function ($query) use ($request, $foodItem) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($foodItem->id),
                ],
                'price' => 'required',
                'cost' => 'required',
                'image' => 'extensions:jpg,jpeg,png,svg,webp'
            ]);
            $requests = $request->all();
            $size = [
                'small' => null,
                'medium' => null,
                'large' => null,
            ];
            $small = isset($requests['small']['selected']);
            $medium = isset($requests['medium']['selected']);
            $large = isset($requests['large']['selected']);
            if (isset($requests['size-status'])) {
                if (($small && $medium) || ($small && $large) || ($medium && $large)) {
                    $size['small'] = $small ? 0 : null;
                    $size['medium'] = $medium ? ($small ? ($requests['medium']['price'] * 1 ?? 0) : 0) : null;
                    $size['large'] = $large ? $requests['large']['price'] * 1 : null;
                }
            }
            $data = [
                'food_category_id' => $requests['categories'],
                'tax_id' => $requests['tax_id'] ?? null,
                'name' => $requests['name'],
                'price' => $requests['price'],
                'cost' => $requests['cost'],
                'status' => $requests['status'],
                'description' => $requests['description'],
                'size' => $size,
                'price_change' => isset($requests['price_change'])
            ];
            if (count($request->files) != 0) {
                $images = $this->itemImageValidated($request);
                if ($images['image']) {
                    $data['image'] = $images['image'];
                    if (!empty($foodItem->image)) {
                        Storage::disk('public')->delete($foodItem->image);
                    }
                }
                if ($images['secondImage']) {
                    $data['second_image'] = $images['secondImage'];
                    if (!empty($foodItem->second_image)) {
                        Storage::disk('public')->delete($foodItem->second_image);
                    }
                }
            }
            $mainLanguageId = config('constants.language.languageId');
            if (!$requests['languageId'] || $requests['languageId'] == $mainLanguageId) {
                FoodItemTranslation::updateOrCreate([
                    'language_id' => $mainLanguageId,
                    'food_item_id' => $foodItem->id,
                ], [
                    'name' => $requests['name'],
                    'description' => $requests['description'],
                ]);
            } else {
                FoodItemTranslation::updateOrCreate([
                    'language_id' => $requests['languageId'],
                    'food_item_id' => $foodItem->id,
                ], [
                    'name' => $requests['name'],
                    'description' => $requests['description'],
                ]);
                unset($data['name'], $data['description']);
            }
            $successUpdate = $foodItem->update($data);
            $this->syncIngredient($request, $foodItem);
            $mealIds = array_map(function ($meal) {
                return $meal['id'];
            }, $foodItem->meals->toArray());
            $message = __('The product is updated!');
            if ($successUpdate) {
                $mealsToUpdate = Meal::whereIn('id', $mealIds)->where('status', true)->get();
                $mealNames = $mealsToUpdate->pluck('name')->toArray();
                if (!empty($mealNames) && $data['status'] == '0') {
                    Meal::whereIn('id', $mealsToUpdate->pluck('id'))->update(['status' => false]);
                    $message = __('The product is updated, and the following meals are set to inactive: ') . implode(', ', $mealNames);
                }
            }
            DB::commit();
            return response()->json(['success', $message]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodItem $foodItem)
    {
        $imagePath = $foodItem->image;
        //        $foodItem->delete();
        $foodItem->forceDelete();
        if (!empty($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        return response()->json(['success' => 'The record is trashed']);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        $models = FoodItem::whereIn('id', $request->ids);
        $imagePaths = $models->pluck('image');
//        $models->delete();
        $models->forceDelete();
        $imagePaths->each(function ($imagePath) {
            if (!empty($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        });
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(FoodItem $foodItem)
    {
        $foodItem->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(FoodItem $foodItem)
    {
        $foodItem->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }

    protected function itemImageValidated($request): array
    {
        $image = null;
        $secondImage = null;
        if ($request->file('image')) {
            $image = $request->file('image')
                ->store('products', 'public');
        }
        if ($request->file('second_image')) {
            $secondImage = $request->file('second_image')
                ->store('products', 'public');
        }
        return ['image' => $image, 'secondImage' => $secondImage];
    }

    protected function syncIngredient($request, $foodItem)
    {
        if ($request['ingredients'] && count($request['ingredients'])) {
            $foodItem->ingredients()->sync([]);
            foreach ($request['ingredients'] as $ingredientId => $value) {
                $foodItem->ingredients()->attach($ingredientId, [
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
}
