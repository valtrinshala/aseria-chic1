<?php

namespace App\Models\AndroidModels;

use App\Models\FoodItemTranslation;
use App\Models\Tax;
use App\Traits\LocationScopeAndSortByNameTraitAndroid;
use App\Traits\LocationScopeTraitAndroid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FoodItem extends Model
{
    use HasFactory, SoftDeletes, LocationScopeAndSortByNameTraitAndroid;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $table = 'food_items';
    protected $with = 'tax';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'tax_id',
        'size',
        'image',
        'food_category_id',
        'description',
        'sku',
        'price'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'cost',
        'status',
        'location_id',
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'size' => 'json',
        'price' => 'float',
        'cost' => 'float',
        'status' => 'boolean'
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
    /**
     * Product tax
     *
     * @return     BelongsTo  The belongs to.
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(\App\Models\AndroidModels\Tax::class, 'tax_id', 'id');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\AndroidModels\Ingredient::class, 'food_items_ingredients')
            ->using(FoodItemIngredientPivot::class)
            ->withPivot('quantity')->withTimestamps();
    }

    public function translate()
    {
        $request = app(Request::class);
        $languageId = $request->language_id;
        $defaultLanguageId = config('constants.language.languageId');
        return $this->hasOne(FoodItemTranslation::class, 'food_item_id')
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
            : asset('images/default/product.png');
    }

    /**
     * User avatar url
     *
     * @return string
     */
    public function getSecondImageAttribute($secondImage): string
    {
        return $secondImage
            ? Storage::disk('public')->url($secondImage)
            : asset('images/default/product.png');
    }

}
