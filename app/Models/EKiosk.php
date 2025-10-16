<?php

namespace App\Models;

use App\Traits\CreatingLocationWithIdUuid;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class EKiosk extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationWithIdUuid, LocationRelationsWithTable;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = ['id', 'user_id', 'name', 'location_id', 'pin_for_settings', 'e_kiosk_id', 'authentication_code', 'description', 'status'];

    protected $casts = [
      'status' => 'boolean'
    ];

    public function user(){
      return $this->belongsTo(User::class, 'user_id');
  }

}
