<?php

namespace App\Models\AndroidModels;

use App\Traits\LocationScopeTraitAndroid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTraitAndroid;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'image',
    ];

    protected $hidden = [
        'location_id',
        'status',
        'created_at',
        'deleted_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => 'boolean'
    ];


    /**
     * User avatar url
     *
     * @return string
     */
    public function getImageAttribute($image): string
    {
        return $image
            ? Storage::disk('public')->url($image)
            : asset('images/default/paymentMethod.png');
    }

}
