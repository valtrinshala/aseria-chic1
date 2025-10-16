<?php

namespace App\Models\AndroidModels;

use App\Traits\LocationScopeTraitAndroid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class EKioskAsset extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTraitAndroid;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'image',
    ];

    protected $hidden = [
        'location_id',
        'e_kiosk_id',
        'name',
        'status',
        'created_at',
        'deleted_at',
        'updated_at',
        'e_kiosk_asset'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(\App\Models\AndroidModels\PositionAsset::class, 'position_id');
    }

    /**
     * User avatar url
     *
     * @return string
     */
    public function getImageAttribute($image): string
    {
        return $image
            ? Storage::disk('public')->url($image) : '';
    }

}
