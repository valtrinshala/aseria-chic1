<?php

namespace App\Models;

use App\Traits\CreatingLocationWithIdUuid;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintSettings extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationWithIdUuid, LocationRelationsWithTable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'device_name',
        'location_id',
        'device_ip',
        'device_port',
        'device_type',
        'device_status',
        'cash_register_or_e_kiosk',
        'cash_register_id',
        'e_kiosk_id',
        'kitchen_id',
        'cash_register_or_e_kiosk_assigned',
        'terminal_compatibility_port',
        'terminal_socket_mode',
        'terminal_type',
        'terminal_id'
    ];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }

    public function eKiosk(): BelongsTo
    {
        return $this->belongsTo(EKiosk::class, 'e_kiosk_id');
    }

    protected $casts = [
        'device_status' => 'boolean',
        'cash_register_or_e_kiosk_assigned' => 'boolean'
    ];
}
