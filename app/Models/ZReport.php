<?php

namespace App\Models;

use App\Traits\CreatingLocationWithIdUuid;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZReport extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationWithIdUuid, LocationRelationsWithTable;
    protected $keyType = "string";
    protected $primaryKey = "id";
    protected $table = 'z_reports';

    protected $fillable = [
        'id',
        'location_id',
        'open_user_id',
        'cash_register_id',
        'report_number',
        'saldo',
        'start_z_report',
        'total_sales',
        'total_balance_with_cash',
        'total_balance_with_card',
        'closing_amount',
        'end_z_report',
        'close_user_id',
    ];

    public function orders(){
        return $this->hasMany(Sale::class, 'z_report_id');
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }
    public function openUser()
    {
        return $this->belongsTo(User::class, 'open_user_id');
    }
    public function closeUser()
    {
        return $this->belongsTo(User::class, 'close_user_id');
    }

}
