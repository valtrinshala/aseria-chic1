<?php

namespace App\Models;

use App\Traits\CreatingLocationWithIdUuid;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationWithIdUuid, LocationRelationsWithTable;

    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = ['id', 'name', 'location_id', 'email', 'phone', 'address', 'shipping_addresses'];


    protected $casts = [
        'shipping_addresses' => 'json',
    ];

    /**
     * Sale under customer
     *
     * @return     HasMany  The has many.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class)->latest()->take(10);
    }

}
