<?php

namespace App\Models;

use App\Traits\CreatingLocationWithIdUuid;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationWithIdUuid, LocationRelationsWithTable;
    protected $keyType = "string";
    protected $primaryKey = "id";

    protected $fillable = [
        'id',
        'location_id',
        'name',
        'suffix',
        'ratio',
        'description'
    ];
}
