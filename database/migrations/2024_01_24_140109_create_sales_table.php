<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('save_order')->default(0);
            $table->index('save_order');
            $table->integer('order_number');
            $table->integer('order_receipt')->nullable()->unique();
            $table->string('type')->nullable();
            $table->index('type');
            $table->string('pos_or_kiosk')->nullable();
            $table->index('pos_or_kiosk');
            $table->uuid('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->index('location_id');
            $table->uuid('z_report_id')->nullable();
            $table->foreign('z_report_id')->references('id')->on('z_reports')->nullOnDelete();
            $table->index('z_report_id');
            $table->uuid('e_kiosk_id')->nullable();
            $table->foreign('e_kiosk_id')->references('id')->on('e_kiosks')->nullOnDelete();
            $table->index('e_kiosk_id');
            $table->string('tracking');
            $table->string('order_type')->default('dine_in');
            $table->index('order_type');
            $table->longText('items');
            $table->text('tax');
            $table->index('tax');
            $table->boolean('is_auto_printed')->default(0);
            $table->index('is_auto_printed');
            $table->decimal('tax_amount')->default(0);
            $table->index('tax_amount');
            $table->boolean('is_paid')->default(0);
            $table->index('is_paid');
            $table->string('que_ready')->default(0);
            $table->integer('cart_total_items')->default(0);
            $table->index('cart_total_items');
            $table->decimal('cart_total_price')->default(0);
            $table->index('cart_total_price');
            $table->decimal('cart_total_cost')->default(0);
            $table->index('cart_total_cost');
            $table->decimal('profit_after_all')->default(0);
            $table->index('profit_after_all');
            $table->decimal('payable_after_all')->default(0);
            $table->index('payable_after_all');
            $table->decimal('discount_rate')->default(0);
            $table->index('discount_rate');
            $table->decimal('discount_amount')->default(0);
            $table->index('discount_amount');
            $table->string('locator')->nullable();
            $table->longText('sum_taxes')->nullable();
            $table->index('sum_taxes');

            $table->uuid('table_id')->nullable();
            $table->foreign('table_id')->references('id')->on('service_tables');
            $table->index('table_id');

            $table->uuid('order_taker_id')->nullable();
            $table->foreign('order_taker_id')->references('id')->on('users');
            $table->timestamp('took_at')->nullable();
            $table->index('took_at');

            $table->boolean('is_preparing')->default(false);
            $table->index('is_preparing');

            $table->uuid('chef_id')->nullable();
            $table->foreign('chef_id')->references('id')->on('users');
            $table->index('chef_id');
            $table->timestamp('prepared_at')->nullable();
            $table->index('prepared_at');

            $table->decimal('after_discount')->default(0);
            $table->index('after_discount');

            $table->uuid('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->boolean('ordered_online')->default(false);

            $table->uuid('biller_id')->nullable();
            $table->foreign('biller_id')->references('id')->on('users');
            $table->index('biller_id');

            $table->timestamp('completed_at')->nullable();
            $table->index('completed_at');


            $table->text('payment_note')->nullable();
            $table->text('staff_note')->nullable();
            $table->float('progress')->default(0);
            $table->index('progress');

            $table->boolean('refund_bank')->default(0);

            $table->uuid('payment_method_id')->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->index('payment_method_id');
            $table->string('payment_method_type')->nullable();
            $table->text('note_for_chef')->nullable();
            $table->boolean('is_cancelled')->default(false);
            $table->index('is_cancelled');
            $table->boolean('is_manual_payment')->default(false);
            $table->boolean('approve_cancel_kitchen')->default(false);
            $table->index('approve_cancel_kitchen');
            $table->text('cancellation_reason')->nullable();
            $table->decimal('cost_during_preparation')->default(0);
            $table->index('cost_during_preparation');
            $table->decimal('paid_cash');
            $table->decimal('paid_bank');
            $table->decimal('payment_return');
            $table->boolean('is_discount_in_percentage')->nullable();
            $table->index('is_discount_in_percentage');
            $table->string('shipping_address')->nullable();
            $table->boolean('payment_status')->default(0);
            $table->longText('payment_data')->nullable();
            $table->longText('payment_data_canceled')->nullable();
            $table->longText('static_data')->nullable();
            $table->index('created_at');
            $table->index('updated_at');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
