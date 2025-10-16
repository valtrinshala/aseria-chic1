<?php

namespace App\Models;

use App\Traits\CreatingLocationWithIdUuid;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Tax extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationWithIdUuid, LocationRelationsWithTable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = ['id', 'tax_rate', 'name', 'type', 'location_id', 'tax_id', 'description', 'tax_calculation', 'tax_included', 'tax_fix_percentage'];
    protected $casts = [
        'tax_calculation' => 'boolean',
        'tax_included' => 'boolean'
    ];

}
