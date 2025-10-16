<?php

namespace App\Models\AndroidModels;

use App\Models\MealTranslation;
use App\Traits\LocationScopeTraitAndroid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class Meal extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTraitAndroid;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $with = 'tax';

    protected $fillable = [
        'id',
        'name',
        'image',
        'tax_id',
        'food_category_id',
        'description',
        'sku',
        'price',
    ];

    protected $casts = [
        'price' => 'float',
        'cost' => 'float',
        'status' => 'boolean'
    ];

    protected $hidden = [
        'location_id',
        'cost',
        'status',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
    /**
     * Product category
     *
     * @return     BelongsTo  The belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\AndroidModels\FoodCategory::class, 'food_category_id', 'id');
    }
    public function tax(): BelongsTo
    {
        return $this->belongsTo(\App\Models\AndroidModels\Tax::class, 'tax_id');
    }
    public function foodItems(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\AndroidModels\FoodItem::class, 'meals_food_items')
            ->using(MealFoodItemPivot::class)
            ->withPivot('quantity')->withTimestamps();
    }

    public function translate()
    {
        $request = app(Request::class);
        $languageId = $request->language_id;
        $defaultLanguageId = config('constants.language.languageId');
        return $this->hasOne(MealTranslation::class, 'meal_id')
            ->where('language_id', $languageId ?? $defaultLanguageId);
    }
    public function getNameAttribute($name)
    {
        return $this->translate ? $this->translate->name : $name;
    }
    public function getDescriptionAttribute($description)
    {
        return $this->translate ? $this->translate->description : $description;
    }

    /**
     * User avatar url
     *
     * @return string
     */
    public function getImageAttribute($image): string
    {
        return $image
            ? Storage::disk('public')->url($image)
            : asset('images/default/deal.jpg');
    }

}
