<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = 'food_categories';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('food_categories', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->string('description')->nullable();
            $table->string('color')->nullable();
            $table->boolean('status')->default(0);
            $table->index('status');
            $table->boolean('category_for_kitchen')->default(1);
            $table->boolean('category_to_ask_for_extra_kitchen')->default(1);
            $table->boolean('category_for_pos')->default(1);
            $table->boolean('category_for_kiosk')->default(1);
            $table->string('name');
            $table->string('image')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('food_categories')->insert([
            ['id' => config('constants.api.drinkId'), 'category_for_kitchen' => false, 'name' => 'Cold drink', 'status' => true, 'color' => '#673ab7'],
            ['id' => config('constants.api.drinkHotId'), 'category_for_kitchen' => false, 'name' => 'Hot drink', 'status' => true, 'color' => '#03a9f4'],
            ['id' => config('constants.api.sauceId'), 'category_for_kitchen' => true, 'name' => 'Sauces', 'status' => true, 'color' => '#8bc34a'],
            ['id' => config('constants.api.sideId'), 'category_for_kitchen' => true, 'name' => 'Side', 'status' => true, 'color' => '#ffeb3b'],
            ['id' => config('constants.api.dessertId'), 'category_for_kitchen' => true, 'name' => 'Dessert', 'status' => true, 'color' => '#9e9e9e'],
            ['id' => config('constants.api.dealId'), 'category_for_kitchen' => true, 'name' => 'Deal', 'status' => true, 'color' => '#9c27b0'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_categories');
    }
};
