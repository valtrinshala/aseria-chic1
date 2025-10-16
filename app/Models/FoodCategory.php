<?php

namespace App\Models;

use App\Traits\CreatingLocationForEveryModelTrait;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeAndSortByName;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodCategory extends Model
{
    use HasFactory, SoftDeletes, LocationScopeAndSortByName, CreatingLocationForEveryModelTrait, LocationRelationsWithTable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'image', 'location_id', 'status', 'category_for_kitchen', 'description', 'color', 'category_to_ask_for_extra_kitchen', 'category_for_pos', 'category_for_kiosk'];

    protected $casts = [
        'status' => 'boolean',
        'category_for_kitchen' => 'boolean',
        'category_to_ask_for_extra_kitchen' => 'boolean',
        'category_for_pos' => 'boolean',
        'category_for_kiosk' => 'boolean'
    ];

    /**
     * Prodcuts undefafar category
     *
     * @return     HasMany  The has many.
     */
    public function products(): HasMany
    {
        return $this->hasMany(FoodItem::class, 'food_category_id', 'id');
    }

    /**
     * Deals undefafar category
     *
     * @return     HasMany  The has many.
     */
    public function deals(): HasMany
    {
        return $this->hasMany(Meal::class, 'food_category_id', 'id');
    }

    public function modifiers()
    {
        return $this->belongsToMany(Modifier::class, 'categories_modifiers', 'category_id', 'modifier_id');
    }
    public function isPrime(){
        $predefinedCategories = config('constants')['api'];
        if (in_array($this->id, array_values($predefinedCategories))){
            return true;
        }else{
            return false;
        }
    }

    public function translate()
    {
        $request = app(Request::class);
        $languageId = $request->query('language_update_id');
        $defaultLanguageId = config('constants.language.languageId');
        return $this->hasOne(FoodCategoryTranslation::class, 'food_category_id')
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
    public function getImageSystem(): string
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
    public function getImage(): string
    {
        $imagePath = $this->image ? Storage::disk('public')->path($this->image) : public_path('images/default/product.png');
        $imageInFile = $this->image ? Storage::disk('public')->exists($this->image) : false;
        if (pathinfo($imagePath, PATHINFO_EXTENSION) === 'svg' && $imageInFile) {
            return file_get_contents($imagePath);
        } else {
            $url = $this->image && $imageInFile ? Storage::disk('public')->url($this->image) : asset('images/default/product.png');
            return '<img src="' . $url . '">';
        }
    }
}
