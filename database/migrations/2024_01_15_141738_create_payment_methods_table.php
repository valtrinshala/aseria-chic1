<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->string('color')->nullable();
            $table->string('status')->default(false);
            $table->index('status');
            $table->softDeletes();
            $table->timestamps();
        });
        DB::table('payment_methods')->insert([
            ['id' => config('constants.paymentMethod.paymentMethodCashId'), 'name' => 'Cash', 'status' => true, 'color' => '#9c27b0' ],
            ['id' => config('constants.paymentMethod.paymentMethodCardId'), 'name' => 'Card', 'status' => true,  'color' => '#4caf50'],
            ['id' => config('constants.paymentMethod.paymentMethodMixId'), 'name' => 'Mix', 'status' => true, 'color' => '#ff5722'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
