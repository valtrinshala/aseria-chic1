<?php

namespace App\Models\AndroidModels;

use App\Models\FoodCategoryTranslation;
use App\Traits\LocationScopeAndSortByNameTraitAndroid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodCategory extends Model
{
    use HasFactory, SoftDeletes, LocationScopeAndSortByNameTraitAndroid;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'image',
        'description',
        'color',
        'category_for_kitchen',
        'category_to_ask_for_extra_kitchen'
    ];

    protected $hidden = [
        'status',
        'location_id',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = ['status' => 'boolean', 'category_for_kitchen' => 'boolean', 'category_to_ask_for_extra_kitchen' => 'boolean'];

    /**
     * Prodcuts undefafar category
     *
     * @return     HasMany  The has many.
     */
    public function products(): HasMany
    {
        return $this->hasMany(\App\Models\AndroidModels\FoodItem::class, 'food_category_id', 'id');
    }

    public function modifiers()
    {
        return $this->belongsToMany(\App\Models\AndroidModels\Modifier::class, 'categories_modifiers', 'category_id', 'modifier_id');
    }
    /**
     * Deals undefafar category
     *
     * @return     HasMany  The has many.
     */
    public function deals(): HasMany
    {
        return $this->hasMany(\App\Models\AndroidModels\Meal::class, 'food_category_id', 'id');
    }
    public function translate()
    {
        $request = app(Request::class);
        $languageId = $request->language_id;
        $defaultLanguageId = config('constants.language.languageId');
        return $this->hasOne(FoodCategoryTranslation::class, 'food_category_id')
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
            : asset('images/default/category.png');
    }
}
