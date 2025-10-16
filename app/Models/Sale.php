<?php

namespace App\Models;

use App\Traits\CreatingLocationForEveryModelTrait;
use App\Traits\LocationRelationsWithTable;
use App\Traits\LocationScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes, LocationScopeTrait, CreatingLocationForEveryModelTrait, LocationRelationsWithTable;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'type',
        'order_number',
        'save_order',
        'locator',
        'sum_taxes',
        'order_id',
        'z_report_id',
        'pos_or_kiosk',
        'e_kiosk_id',
        'tracking',
        'order_receipt',
        'location_id',
        'took_at', 'order_taker_id', 'order_type',
        'cart_total_cost',
        'cart_total_items',
        'cart_total_price',
        'items',
        'profit_after_all',
        'payable_after_all',
        'tax',
        'tax_amount',
        'is_paid',
        'que_ready',
        'discount_rate',
        'discount_amount',
        'table_id',
        'is_preparing',
        'chef_id',
        'prepared_at',
        'customer_id',
        'ordered_online',
        'biller_id',
        'completed_at',
        'payment_note', 'progress',
        'staff_note', 'payment_method_id', 'payment_method_type', 'note_for_chef', 'is_cancelled', 'is_manual_payment',
        'cancellation_reason', 'cost_during_preparation',
        'paid_cash','paid_bank','payment_return',
        'approve_cancel_kitchen', 'is_discount_in_percentage', 'pickup_point_id', 'shipping_address',
        'payment_status', 'payment_data', 'payment_data_canceled',
        'static_data',
        'is_auto_printed',
        'refund_bank'
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'items' => 'json',
        'sum_taxes' => 'json',
        'tax' => 'json',
        'static_data' => 'json',
        'is_preparing' => 'boolean',
        'is_cancelled' => 'boolean',
        'is_discount_in_percentage' => 'boolean',
        'save_order' => 'boolean',
        'approve_cancel_kitchen' => 'boolean',
        'is_paid' => 'boolean',
        'payment_status' => 'boolean',
        'payment_data' => 'json',
        'payment_data_canceled' => 'json',
        'refund_bank' => 'boolean',
        'que_ready' => 'boolean',
        'is_auto_printed' => 'boolean',
        'is_manual_payment' => 'boolean'
    ];

    public function serviceTable(): BelongsTo
    {
        return $this->belongsTo(ServiceTable::class, 'table_id');
    }

//    public function pickupPoint(): BelongsTo
//    {
//        return $this->belongsTo(PickupPoint::class, 'pickup_point_id');
//    }

    public function scopeOrderForKitchen($query)
    {
        return $query->where('is_cancelled', false)->whereNull('prepared_at');
    }

    public function scopeSubmittedOrder($query)
    {
        return $query->where('is_cancelled', false)->whereNull('completed_at');
    }

    public function scopeOrderForBilling($query)
    {
        return $query->where('is_preparing', true)
            ->whereNull('biller_id')
            ->where('is_cancelled', false)
            ->whereNull('completed_at')
            ->whereNotNull('prepared_at')
            ->whereNotNull('chef_id');
    }

   public function customer(): BelongsTo
   {
       return $this->belongsTo(Customer::class);
   }

    public function zReport(){
        return $this->belongsTo(ZReport::class, 'z_report_id');
    }

    public function taker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'order_taker_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function biller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'biller_id');
    }

    public function chef(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

     public function eKiosk(): BelongsTo
     {
         return $this->belongsTo(EKiosk::class, 'e_kiosk_id');
     }

    /**
     * Get the users for the user role
     *
     * @return HasMany
     */
    public function CardPaymentDetails(): HasMany
    {
        return $this->hasMany(CardPaymentDetails::class, 'sale_id', 'id');
    }

//    public function getType()
//    {
//        $types = collect([
//            ['title' => __('Dining'), 'key' => 'dining'],
//            ['title' => __('Pickup'), 'key' => 'pickup'],
//            ['title' => __('Delivery'), 'key' => 'dilivery'],
//        ]);
//        $matchedTitle = $types->where(['is_cancelled' => false, 'is_paid' => true, 'key' => $this->order_type])->pluck('title')->first();
//        return $matchedTitle;
//    }

    public function scopeDuration($query, $value, $isLast = false)
    {
        $date = now();
        if ($isLast) {
            if ('year' == $value) {
                $date = $date->subYear();
            } elseif ('month' == $value) {
                $date = $date->subMonth();
            } elseif ('day' == $value) {
                $date = $date->subDay();
            }
        }

        if ('day' == $value) {
            return $query->whereNotNull('completed_at')->where(['is_cancelled' => false, 'is_paid' => true])->whereDate('created_at', '=', $date->format('Y-m-d'));
        }
        if ('month' == $value) {
            return $query->whereNotNull('completed_at')->where(['is_cancelled' => false, 'is_paid' => true])->whereMonth('created_at', '=', $date->month);
        }
        if ('year' == $value) {
            return $query->whereNotNull('completed_at')->where(['is_cancelled' => false, 'is_paid' => true])->whereYear('created_at', '=', $date->year);
        }

        return $query;
    }
}
