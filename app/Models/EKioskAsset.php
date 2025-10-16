<?php

namespace App\Models;

use App\Traits\CreatingLocationWithIdUuid;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class EKioskAsset extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationWithIdUuid, LocationRelationsWithTable;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = ['id', 'name', 'location_id', 'image', 'e_kiosk_id', 'position_id', 'type', 'status'];

    protected $casts = [
        'status' => 'boolean'
    ];
    public function position(): BelongsTo
    {
        return $this->belongsTo(PositionAsset::class, 'position_id');
    }


    public function eKiosk(): BelongsTo
    {
        return $this->belongsTo(EKiosk::class, 'e_kiosk_id');
    }

    public function getImage()
    {
        return $this->image ? Storage::disk('public')->url($this->image) : asset('images/default/product.png');
    }
}
