<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class FoodCategoryTranslation extends Model
{

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'description',
        'language_id',
        'food_category_id',
    ];
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $uuid = Uuid::uuid4();
            $model->id = $uuid->toString();
        });
    }
}
