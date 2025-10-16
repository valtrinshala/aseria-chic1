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
        Schema::create('print_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->string('device_name')->nullable();
            $table->string('device_ip')->nullable();
            $table->string('device_port')->nullable();
            $table->string('device_type')->nullable();
            $table->boolean('device_status')->default(true);
            $table->string('terminal_compatibility_port')->nullable();
            $table->string('terminal_socket_mode')->nullable();
            $table->string('terminal_type')->nullable();
            $table->string('terminal_id')->nullable();
            $table->string('cash_register_or_e_kiosk')->nullable();
            $table->uuid('cash_register_id')->nullable();
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->nullOnDelete();
            $table->uuid('e_kiosk_id')->nullable();
            $table->string('kitchen_id')->nullable();
            $table->foreign('e_kiosk_id')->references('id')->on('e_kiosks')->nullOnDelete();
            $table->boolean('cash_register_or_e_kiosk_assigned')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_settings');
    }
};
