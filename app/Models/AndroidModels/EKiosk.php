<?php

namespace App\Models\AndroidModels;

use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EKiosk extends Model
{
    use HasApiTokens, HasFactory, Notifiable, LocationRelationsWithTable, LocationScopeTrait;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = ['id', 'user_id', 'name', 'location_id', 'e_kiosk_id', 'pin_for_settings', 'authentication_code', 'description', 'status'];

    protected $casts = [
        'status' => 'boolean'
    ];
}
