<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('z_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->uuid('open_user_id')->nullable();
            $table->foreign('open_user_id')->references('id')->on('users')->nullOnDelete();
            $table->uuid('cash_register_id')->nullable();
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->nullOnDelete();
            $table->decimal('report_number')->default(0);
            $table->decimal('saldo')->default(0);
            $table->timestamp('start_z_report')->nullable();
            $table->decimal('total_sales')->default(0);
            $table->decimal('total_balance_with_cash')->default(0);
            $table->decimal('total_balance_with_card')->default(0);
            $table->decimal('closing_amount')->default(0);
            $table->timestamp('end_z_report')->nullable();
            $table->uuid('close_user_id')->nullable();
            $table->foreign('close_user_id')->references('id')->on('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('z_reports');
    }
};
