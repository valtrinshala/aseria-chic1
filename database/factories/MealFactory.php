<?php

namespace Database\Factories;

use App\Models\FoodCategory;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class MealFactory extends Factory
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function definition(): array
    {

        $locationId = [];
        foreach (Location::all() as $location){
            $locationId[] = $location->id;
        }

        $foodCategoryId =[];
        foreach (FoodCategory::all() as $foodCategory){
            $foodCategoryId[] = $foodCategory->id;
        }

        $name = [
            "veggie-primavera",
            "shrimp-scampi",
            "t-bone-steak",
            "prime-rib",
            "steak-frites",
          ];

        return [
            'id' => Uuid::uuid4(),
            'food_category_id'=> $this->faker->randomElement($foodCategoryId),
            'location_id' =>  $this->faker->randomElement($locationId),
            'name' => $this->faker->randomElement($name),
            'sku' => $this->faker->regexify('[A-Z0-9]{10}'),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'cost' => $this->faker->randomFloat(2, 0, 100),
            'status' => $this->faker->randomElement(['Published', 'Inactive']),
            'description' => $this->faker->sentence(5),
        ];
    }
}
