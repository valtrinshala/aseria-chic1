<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'location',
        'pos',
        'kitchen',
        'integrated_payments',
        'manual_payments',
        'has_tables',
        'has_locators',
        'e_kiosk',
        'dine_in',
        'take_away',
        'delivery',
        'auto_print'
    ];

    protected $casts = [
        'pos' => 'boolean',
        'kitchen' => 'boolean',
        'e_kiosk' => 'boolean',
        'dine_in' => 'boolean',
        'integrated_payments' => 'boolean',
        'manual_payments' => 'boolean',
        'take_away' => 'boolean',
        'delivery' => 'boolean',
        'has_tables' => 'boolean',
        'has_locators' => 'boolean',
        'auto_print' => 'boolean',
    ];

    public function isPrime(): bool
    {
        return config('constants.location.defaultLocationId') === $this->id;
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $uuid = Uuid::uuid4();
            $model->id = $uuid->toString();
        });
    }
}
