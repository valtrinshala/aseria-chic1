<?php

namespace App\Models;

use App\Traits\CreatingLocationWithIdUuid;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionAsset extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationWithIdUuid, LocationRelationsWithTable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'asset_key',
        'name',
        'description',
        'status',
        'location_id',
    ];
    protected $casts = [
        'status' => 'boolean'
    ];

    // public function eKiosk(): BelongsTo
    // {
    //     return $this->belongsTo(EKiosk::class, 'e_kiosk_id');
    // }

    public function eKioskAsset(): HasMany
    {
        return $this->hasMany(EKioskAsset::class, 'position_id');
    }

}
