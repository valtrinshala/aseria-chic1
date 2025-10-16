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
        Schema::create('categories_modifiers', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->default(DB::raw('UUID()'));
            $table->uuid('modifier_id')->nullable();
            $table->uuid('category_id')->nullable();
            $table->string('quantity')->default(0);
            $table->foreign('modifier_id')->references('id')->on('modifiers')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('food_categories')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories_modifiers');
    }
};
