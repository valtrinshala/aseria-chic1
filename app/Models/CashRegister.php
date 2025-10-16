<?php

namespace App\Models;

use App\Traits\CreatingLocationWithIdUuid;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashRegister extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationWithIdUuid, LocationRelationsWithTable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $table = 'cash_registers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id', 'user_id', 'location_id', 'pin_for_settings', 'name', 'key', 'status', 'description', 'pin', 'pin_to_print_reports'];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}
