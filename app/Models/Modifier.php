<?php

namespace App\Models;

use App\Traits\CreatingLocationForEveryModelTrait;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeAndSortByTitle;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Modifier extends Model
{
    use HasFactory, SoftDeletes, LocationScopeAndSortByTitle, CreatingLocationForEveryModelTrait, LocationRelationsWithTable;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'location_id',
        'title',
        'image',
        'description',
        'sku',
        'price',
        'cost',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'ingredients_modifiers')->withPivot('quantity', 'unit', 'cost', 'price', 'cost_per_unit', 'price_per_unit')->withTimestamps();
    }

    public function category(): BelongsToMany
    {
        return $this->belongsToMany(FoodCategory::class, 'categories_modifiers', 'modifier_id', 'category_id')->withTimestamps();
    }

    public function translate()
    {
        $request = app(Request::class);
        $languageId = $request->query('language_update_id');
        $defaultLanguageId = config('constants.language.languageId');
        return $this->hasOne(ModifierTranslation::class, 'modifier_id')
            ->where('language_id', $languageId ?? session()->get('language_id') ?? $defaultLanguageId);
    }

    public function getTitleAttribute($title)
    {
        return $this->translate ? $this->translate->title : $title;
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
        $imagePath = $this->image ? Storage::disk('public')->path($this->image) : public_path('images/default/product.png');
        if (pathinfo($imagePath, PATHINFO_EXTENSION) === 'svg') {
            return file_get_contents($imagePath);
        } else {
            return $this->image ? Storage::disk('public')->url($this->image) : asset('images/default/product.png');
        }
    }
}
