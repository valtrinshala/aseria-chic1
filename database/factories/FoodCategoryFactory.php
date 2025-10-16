<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class FoodCategoryFactory extends Factory
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
        $name = [
            "Sandwiches",
            "Salads",
            "Burgers",
            "Appetizers",
            "Soups",
            "Pizzas",
            "Pasta",
          ];
          $colors = ['#5D4BDF', '#3A5C48', '#D17FB2'  ];

        return [
            'id' => Uuid::uuid4(),
            'location_id' =>  $this->faker->randomElement($locationId),
            'description' => $this->faker->sentence(5),
            'color' => $this->faker->randomElement($colors),
            'status' => $this->faker->randomElement([0, 1]),
            'name' => $this->faker->randomElement($name),
        ];
    }
}
