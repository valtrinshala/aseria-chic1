<?php

namespace App\Models;

use App\Traits\CreatingLocationForEveryModelTrait;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeAndSortByName;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Ingredient extends Model
{
    use HasFactory, SoftDeletes, LocationScopeAndSortByName, CreatingLocationForEveryModelTrait, LocationRelationsWithTable;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id','location_id', 'name', 'price', 'cost', 'quantity', 'alert_quantity', 'unit'];

    public function foodItems(): BelongsToMany
    {
        return $this->belongsToMany(FoodItem::class, 'food_items_ingredients');
    }

    public function modifiers(): BelongsToMany
    {
        return $this->belongsToMany(Modifier::class, 'ingredients_modifiers');
    }
    public function translate()
    {
        $request = app(Request::class);
        $languageId = $request->query('language_update_id');
        $defaultLanguageId = config('constants.language.languageId');
        return $this->hasOne(IngredientTranslation::class, 'ingredient_id')
            ->where('language_id', $languageId ?? session()->get('language_id') ?? $defaultLanguageId);
    }
    public function getNameAttribute($name)
    {
        return $this->translate ? $this->translate->name : $name;
    }
}
