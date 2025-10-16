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
        Schema::create('ingredients_modifiers', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->default(DB::raw('UUID()'));
            $table->uuid('modifier_id')->nullable();
            $table->uuid('ingredient_id')->nullable();
            $table->string('quantity')->default(0);
            $table->string('unit')->nullable();
            $table->string('cost')->nullable();
            $table->string('price')->nullable();
            $table->string('cost_per_unit')->nullable();
            $table->string('price_per_unit')->nullable();
            $table->foreign('modifier_id')->references('id')->on('modifiers')->onDelete('cascade');
            $table->foreign('ingredient_id')->references('id')->on('ingredients')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients_modifiers');
    }
};
