<?php

namespace App\Models\AndroidModels;

use App\Models\IngredientTranslation;
use App\Traits\LocationScopeAndSortByNameTraitAndroid;
use App\Traits\LocationScopeTraitAndroid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Ingredient extends Model
{
    use HasFactory, SoftDeletes, LocationScopeAndSortByNameTraitAndroid;
    protected $primaryKey = 'id';
    protected $keyType = 'string';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id', 'name', 'price', 'quantity', 'alert_quantity', 'unit'];

    protected $hidden = ['cost', 'quantity','location_id', 'alert_quantity', 'deleted_at', 'created_at', 'updated_at'];

    protected $casts = [
        'price' => 'float',
        'cost' => 'float',
        'quantity' => 'integer',
    ];

    public function foodItems(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\AndroidModels\FoodItem::class, 'food_items_ingredients')->withPivot('quantity');
    }

    public function modifiers(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\AndroidModels\Modifier::class, 'ingredients_modifiers');
    }

    public function translate()
    {
        $request = app(Request::class);
        $languageId = $request->language_id;
        $defaultLanguageId = config('constants.language.languageId');
        return $this->hasOne(IngredientTranslation::class, 'ingredient_id')
            ->where('language_id', $languageId ?? $defaultLanguageId);
    }
    public function getNameAttribute($name)
    {
        return $this->translate ? $this->translate->name : $name;
    }
}
