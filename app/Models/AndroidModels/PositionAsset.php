<?php

namespace App\Models\AndroidModels;

use App\Models\EKiosk;
use App\Traits\LocationScopeTraitAndroid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionAsset extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTraitAndroid;

    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'asset_key',
        'url',
        'type',
    ];
    protected $casts = [
        'status' => 'boolean'
    ];

    protected $hidden = [
        'description',
        'location_id',
        'e_kiosk_id',
        'name',
        'status',
        'created_at',
        'deleted_at',
        'updated_at',
    ];

    public function eKiosk(): BelongsTo
    {
        return $this->belongsTo(EKiosk::class, 'e_kiosk_id');
    }

    public function eKioskAsset(): HasOne
    {
        return $this->hasOne(\App\Models\AndroidModels\EKioskAsset::class, 'position_id');
    }
}
