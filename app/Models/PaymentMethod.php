<?php

namespace App\Models;

use App\Traits\CreatingLocationForEveryModelTrait;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationForEveryModelTrait, LocationRelationsWithTable;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'location_id', 'status', 'image', 'color'];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function isPrime(){
        $predefinedPaymentMethods = config('constants')['paymentMethod'];
        if (in_array($this->id, array_values($predefinedPaymentMethods))){
            return true;
        }else{
            return false;
        }
    }
 /**
     * User avatar url
     *
     * @return string
     */
    public function getImage(): string
    {
        return $this->image
            ? Storage::disk('public')->url($this->image)
            : asset('images/default/product.png');
    }
}
