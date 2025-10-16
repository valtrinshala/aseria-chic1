<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class LocationFactory extends Factory
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function definition(): array
    {

      $name = [
        "Prishtina Mall",
        "Albi Mall",
        "Royal Mall",
      ];

      return [
        'id' => Uuid::uuid4(),
        'location' =>  $this->faker->randomElement(['Prishtine', 'Prizren']),
        'name' => $this->faker->randomElement($name),
        'pos' => $this->faker->randomElement([0, 1]),
        'kitchen' => $this->faker->randomElement([0, 1])
      ];
    }
}
