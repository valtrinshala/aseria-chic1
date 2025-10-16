<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class IngredientFactory extends Factory
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
            "Chicken",
            "Potato",
            "Bread",
            "Onion",
            'Biscuits'
          ];

        return [
            'id' => Uuid::uuid4(),
            'location_id' =>  $this->faker->randomElement($locationId),
            'name' => $this->faker->randomElement($name),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'cost' => $this->faker->randomFloat(2, 0, 100),
            'unit' => $this->faker->randomElement(['kg', 'L', 'm']),
            'quantity' => $this->faker->numberBetween(0, 500),
            'alert_quantity'=> $this->faker-> numberBetween(0, 100),
        ];
    }
}
