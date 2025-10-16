<?php

namespace App\Models\AndroidModels;

use App\Models\ModifierTranslation;
use App\Traits\LocationScopeAndSortByNameTraitAndroid;
use App\Traits\LocationScopeAndSortByTitleTraitAndroid;
use App\Traits\LocationScopeTraitAndroid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Modifier extends Model
{
    use HasFactory, SoftDeletes, LocationScopeAndSortByTitleTraitAndroid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'title',
        'price',
        'description',
    ];

    protected $hidden  = [
        'location_id',
        'cost',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'price' => 'float',
        'cost' => 'float',
        'status' => 'boolean'
    ];

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\AndroidModels\Ingredient::class, 'ingredients_modifiers')
            ->using(ModifierIngredientPivot::class)
            ->withPivot('quantity')->withTimestamps();
    }

    public function category(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\AndroidModels\FoodCategory::class, 'categories_modifiers', 'modifier_id', 'category_id')->withTimestamps();
    }

    public function translate()
    {
        $request = app(Request::class);
        $languageId = $request->language_id;
        $defaultLanguageId = config('constants.language.languageId');
        return $this->hasOne(ModifierTranslation::class, 'modifier_id')
            ->where('language_id', $languageId ?? $defaultLanguageId);
    }
    public function getTitleAttribute($title)
    {
        return $this->translate ? $this->translate->title : $title;
    }
    public function getDescriptionAttribute($description)
    {
        return $this->translate ? $this->translate->description : $description;
    }
}
