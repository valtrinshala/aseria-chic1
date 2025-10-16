<?php

namespace App\Models;

use App\Traits\CreatingLocationForEveryModelTrait;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeAndSortByName;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\Concerns\Has;
use Ramsey\Uuid\Uuid;

class FoodItem extends Model
{
    use HasFactory, SoftDeletes, LocationScopeAndSortByName, CreatingLocationForEveryModelTrait, LocationRelationsWithTable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $with = 'tax';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'location_id',
        'name',
        'tax_id',
        'image',
        'second_image',
        'size',
        'food_category_id',
        'description',
        'sku',
        'price',
        'cost',
        'price_change',
        'status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'size' => 'json',
        'status' => 'boolean',
        'price_change' => 'boolean'
    ];
    /**
     * Product category
     *
     * @return     BelongsTo  The belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class, 'food_category_id', 'id');
    }
    /**
     * Product tax
     *
     * @return     BelongsTo  The belongs to.
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }
    /**
     * Product category
     *
     * @return     BelongsToMany  The belongs to many.
     */
    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meals_food_items');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'food_items_ingredients')->withPivot('quantity', 'unit', 'cost', 'price', 'cost_per_unit', 'price_per_unit')->withTimestamps();
    }

    public function translate()
    {
        $request = app(Request::class);
        $languageId = $request->query('language_update_id');
        $defaultLanguageId = config('constants.language.languageId');
        return $this->hasOne(FoodItemTranslation::class, 'food_item_id')
            ->where('language_id', $languageId ?? session()->get('language_id') ?? $defaultLanguageId);
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
    public function getImage(): string
    {
        return $this->image
            ? Storage::disk('public')->url($this->image)
            : asset('images/default/product.png');
    }

    /**
     * User avatar url
     *
     * @return string
     */
    public function getSecondImage(): string
    {
        return $this->second_image
            ? Storage::disk('public')->url($this->second_image)
            : asset('images/default/product.png');
    }

}
