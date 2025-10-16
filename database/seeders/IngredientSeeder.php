<?php

namespace Database\Seeders;

use App\Models\EKiosk;
use App\Models\FoodCategory;
use App\Models\FoodItem;
use App\Models\Ingredient;
use App\Models\Location;
use App\Models\Meal;
use App\Models\Modifier;
use App\Models\PaymentMethod;
use App\Models\ServiceTable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *  php artisan db:seed --class=IngredientSeeder
     */
    public function run(): void
    {
        Location::factory()->count(3)->create();
        Ingredient::factory()->count(80)->create();
        Modifier::factory()->count(40)->create();
        FoodCategory::factory()->count(20)->create();
        FoodItem::factory()->count(80)->create();
        Meal::factory()->count(40)->create();
        EKiosk::factory()->count(10)->create();
        PaymentMethod::factory()->count(10)->create();
    }
}
