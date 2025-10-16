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
        Schema::create('food_category_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('language_id')->nullable();
            $table->foreign('language_id')->references('id')->on('languages')->nullOnDelete();
            $table->uuid('food_category_id')->nullable();
            $table->foreign('food_category_id')->references('id')->on('food_categories')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        $mainLanguageId = config('constants.language.languageId');
        DB::table('food_category_translations')->insert([
            ['id' => Uuid::uuid4()->toString(), 'language_id' => $mainLanguageId ,'food_category_id' => config('constants.api.drinkId'), 'name' => 'Drink cold'],
            ['id' => Uuid::uuid4()->toString(), 'language_id' => $mainLanguageId ,'food_category_id' => config('constants.api.drinkHotId'), 'name' => 'Drink hot'],
            ['id' => Uuid::uuid4()->toString(), 'language_id' => $mainLanguageId ,'food_category_id' => config('constants.api.sauceId') , 'name' => 'Sauces'],
            ['id' => Uuid::uuid4()->toString(), 'language_id' => $mainLanguageId ,'food_category_id' => config('constants.api.sideId') , 'name' => 'Side'],
            ['id' => Uuid::uuid4()->toString(), 'language_id' => $mainLanguageId ,'food_category_id' => config('constants.api.dessertId') , 'name' => 'Dessert'],
            ['id' => Uuid::uuid4()->toString(), 'language_id' => $mainLanguageId ,'food_category_id' => config('constants.api.dealId') , 'name' => 'Deal'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_category_translations');
    }
};
