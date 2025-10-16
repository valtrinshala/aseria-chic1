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
        Schema::create('food_item_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('language_id')->nullable();
            $table->foreign('language_id')->references('id')->on('languages')->nullOnDelete();
            $table->uuid('food_item_id')->nullable();
            $table->foreign('food_item_id')->references('id')->on('food_items')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_item_translations');
    }
};
