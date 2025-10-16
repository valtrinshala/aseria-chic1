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

class SystemAsset extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationWithIdUuid, LocationRelationsWithTable;

    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = ['id', 'name', 'location_id', 'image', 'cash_register_id', 'status'];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }

    public function getImage()
    {
        return $this->image ? Storage::disk('public')->url($this->image) : asset('images/default/product.png');
    }
}
