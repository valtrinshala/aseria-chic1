<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use App\Models\FoodCategoryTranslation;
use App\Models\FoodItem;
use App\Models\Meal;
use App\Models\ZReport;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use Illuminate\Validation\Rule;

class FoodCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $foodCategories = FoodCategory::get();
        return view('food-categories/foodCategory-index', compact('foodCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
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
            return view($data ? 'food-categories/foodCategory-create' : 'pop-up-locations', compact('colors'));
        }
        return view('food-categories/foodCategory-create', compact('colors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => ['required', new UniqueNameForLocation(FoodCategory::class)],
                'image' => 'required|extensions:jpg,jpeg,png,svg,webp'
            ]);
            $data = $request->all();
            if ($request->file('image')) {
                $data['image'] = $this->categoryValidated($request);
            }
            $data['id'] = Uuid::uuid4()->toString();
            isset($data['category_for_kitchen']) ? $data['category_for_kitchen'] = 1 : $data['category_for_kitchen'] = 0;
            isset($data['category_to_ask_for_extra_kitchen']) ? $data['category_to_ask_for_extra_kitchen'] = 1 : $data['category_to_ask_for_extra_kitchen'] = 0;
            isset($data['category_for_pos']) ? $data['category_for_pos'] = 1 : $data['category_for_pos'] = 0;
            isset($data['category_for_kiosk']) ? $data['category_for_kiosk'] = 1 : $data['category_for_kiosk'] = 0;
            FoodCategory::create($data);
            FoodCategoryTranslation::create([
                'language_id' => config('constants.language.languageId'),
                'food_category_id' => $data['id'],
                'name' => $data['name'],
                'description' => $data['description'],
            ]);
            DB::commit();
            return response()->json(['success', 'The category has been created']);
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
    public function edit(FoodCategory $foodCategory)
    {
        $colors = [
            ['#f44336', '#e81e63', '#9c27b0'],
            ['#673ab7', '#3f51b5', '#2196f3'],
            ['#03a9f4', '#00bcd4', '#009688'],
            ['#4caf50', '#8bc34a', '#cddc39'],
            ['#ffeb3b', '#ffc107', '#ff9800'],
            ['#ff5722', '#795548', '#9e9e9e'],
        ];
        return view('food-categories/foodCategory-edit', compact('foodCategory', 'colors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FoodCategory $foodCategory)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => [
                    'required',
                    Rule::unique('food_categories')->where(function ($query) use ($request, $foodCategory) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($foodCategory->id),
                ],
                'image' => 'extensions:jpg,jpeg,png,svg,webp'
            ]);
            $data = $request->all();
            if (!(isset($data['category_for_kitchen']) && $foodCategory->category_for_kitchen == $data['category_for_kitchen'])){
                if (ZReport::where('end_z_report', null)->first()){
                    return response()->json(['errors' => ['name' => [__("You cannot change 'Category for kitchen' because you have zReport open yet, and it can affect the orders that are in process")]]], 422);
                }
            }
            if ($request->file('image')) {
                $data['image'] = $this->categoryValidated($request);
                if (!empty($foodCategory->image)) {
                    Storage::disk('public')->delete($foodCategory->image);
                }
            }
            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            isset($data['category_for_kitchen']) ? $data['category_for_kitchen'] = 1 : $data['category_for_kitchen'] = 0;
            isset($data['category_to_ask_for_extra_kitchen']) ? $data['category_to_ask_for_extra_kitchen'] = 1 : $data['category_to_ask_for_extra_kitchen'] = 0;
            isset($data['category_for_pos']) ? $data['category_for_pos'] = 1 : $data['category_for_pos'] = 0;
            isset($data['category_for_kiosk']) ? $data['category_for_kiosk'] = 1 : $data['category_for_kiosk'] = 0;
            $mainLanguageId = config('constants.language.languageId');
            if (!$data['languageId'] || $data['languageId'] == $mainLanguageId){
                FoodCategoryTranslation::updateOrCreate([
                    'language_id' => $mainLanguageId,
                    'food_category_id' => $foodCategory->id,
                ], [
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]);
            }else{
                FoodCategoryTranslation::updateOrCreate([
                    'language_id' => $data['languageId'],
                    'food_category_id' => $foodCategory->id,
                ], [
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]);
                unset($data['name'], $data['description']);
            }
            $foodCategory->update($data);
            if ($data['status'] === 0){
                $productIds = [];
                $products = $foodCategory->products;
                foreach ($products as $product) {
                    $productIds[] = $product->id;
                    $product->meals()->update(['status' => 0]);
                }
                FoodItem::whereIn('id', $productIds)->update(['status' => 0]);
            }
            DB::commit();
            return response()->json(['success', 'The category food has been updated']);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodCategory $foodCategory)
    {

        if ($foodCategory->isPrime()) {
            return response()->json(['error' => "You cannot delete this category because it is predefined"], 422);
        }
        $imagePath = $foodCategory->image;
        // $foodCategory->delete();
        $foodCategory->forceDelete();
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
        $categoryIds = $request->ids;
        $error = false;
        $categories = [];
        foreach ($categoryIds as $categoryId) {
            $language = FoodCategory::find($categoryId);
            if ($language?->isPrime()) {
                $error = true;
            } elseif ($language) {
                $categories[] = $language->id;
            }
        }
        $statusCode = $error ? 422 : 200;


        $models = FoodCategory::whereIn('id', $categories);
        $imagePaths = $models->pluck('image');
//        $models->delete();
        $models->forceDelete();
        $imagePaths->each(function ($imagePath) {
            if (!empty($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        });
        return response()->json(!$error ? (['success' => 'The records are trashed']) : ['error' => 'Categories are deleted except for the predefined categories, which you cannot delete'], $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(FoodCategory $foodCategory)
    {
        $foodCategory->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(FoodCategory $foodCategory)
    {
        $foodCategory->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }


    protected function categoryValidated($request): string
    {
        return $request->file('image')
            ->store('food/categories', 'public');
    }
}
