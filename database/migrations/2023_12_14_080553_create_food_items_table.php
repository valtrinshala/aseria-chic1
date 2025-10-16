<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = 'food_items';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('food_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('food_category_id')->nullable();
            $table->foreign('food_category_id')->references('id')->on('food_categories')->nullOnDelete();
            $table->uuid('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->text('size')->nullable();
            $table->string('sku')->nullable();
            $table->string('name');
            $table->index('name');
            $table->uuid('tax_id')->nullable();
            $table->foreign('tax_id')->references('id')->on('taxes')->nullOnDelete();
            $table->string('price')->default(1);
            $table->boolean('price_change')->default(0);
            $table->index('price_change');
            $table->string('cost')->default(0);
            $table->string('status')->nullable();
            $table->index('status');
            $table->string('image')->nullable();
            $table->string('second_image')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};
