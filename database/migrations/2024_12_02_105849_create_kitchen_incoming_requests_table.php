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
        Schema::create('kitchen_incoming_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('location')->nullable();
            $table->string('user_create_name')->nullable();
            $table->string('kitchen_name')->nullable();
            $table->string('device_id')->unique()->nullable();
            $table->string('authentication_code')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_incoming_requests');
    }
};
