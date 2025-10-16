<?php

namespace App\Models;

use App\Traits\CreatingLocationForEveryModelTrait;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Meal extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationForEveryModelTrait, LocationRelationsWithTable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $with = 'tax';

    protected $fillable = [
        'id',
        'location_id',
        'name',
        'image',
        'tax_id',
        'food_category_id',
        'description',
        'sku',
        'price',
        'cost',
        'status'
    ];
    protected $casts = [
        'status' => 'boolean'
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
     * Meal tax
     *
     * @return     BelongsTo  The belongs to.
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }
    public function foodItems(): BelongsToMany
    {
        return $this->belongsToMany(FoodItem::class, 'meals_food_items')->withPivot('quantity')->withTimestamps();
    }

    public function translate()
    {
        $request = app(Request::class);
        $languageId = $request->query('language_update_id');
        $defaultLanguageId = config('constants.language.languageId');
        return $this->hasOne(MealTranslation::class, 'meal_id')
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

    public function getImage(): string
    {
        return $this->image
            ? Storage::disk('public')->url($this->image)
            : asset('images/default/product.png');
    }

}
