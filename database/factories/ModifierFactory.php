<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class ModifierFactory extends Factory
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
        $title = [
            "Cap katchup",
            "Creamer",
            "Sugar",
            "Mayo",
          ];

        return [
            'id' => Uuid::uuid4(),
            'location_id' =>  $this->faker->randomElement($locationId),
            'title' => $this->faker->randomElement($title),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'cost' => $this->faker->randomFloat(2, 0, 100),
            'status' => $this->faker->randomElement(['Published', 'Inactive']),
        ];
    }
}
