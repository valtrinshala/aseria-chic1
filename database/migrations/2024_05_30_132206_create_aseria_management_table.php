<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('aseria_management', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('UUID()'));
            $table->string('image')->nullable();
            $table->string('second_image')->nullable();
            $table->string('company_name')->nullable();
            $table->string('website')->nullable();
            $table->string('working_hours')->nullable();
            $table->string('working_days')->nullable();
            $table->string('telephone')->nullable();
            $table->string('urgent_calls')->nullable();
            $table->string('email')->nullable();
            $table->string('description', 500)->nullable();
            $table->timestamps();
        });
        DB::table('aseria_management')->insert([
            [],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aseria_management');
    }
};
