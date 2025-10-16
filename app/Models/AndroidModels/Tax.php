<?php

namespace App\Models\AndroidModels;

use App\Traits\LocationScopeTraitAndroid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Tax extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTraitAndroid;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = ['tax_rate', 'tax_id', 'name', 'type', 'description'];
    protected $hidden = [
        'location_id',
        'tax_calculation',
        'tax_included',
        'tax_fix_percentage',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'tax_calculation' => 'boolean',
        'tax_included' => 'boolean',
        'tax_rate' => 'float'
    ];

}
