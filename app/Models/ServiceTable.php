<?php

namespace App\Models;

use App\Traits\CreatingLocationWithIdUuid;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeAndSortByTitle;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class ServiceTable extends Model
{
    use HasFactory, SoftDeletes, LocationScopeAndSortByTitle, CreatingLocationWithIdUuid, LocationRelationsWithTable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = ['id', 'title', 'location_id', 'is_booked', 'order_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_booked' => 'boolean',
    ];

}
