<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class EKioskFactory extends Factory
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
            "Stability",
            "Software",
            "Emergency",
            "Management",
          ];

        return [
            'id' => Uuid::uuid4(),
            'location_id' =>  $this->faker->randomElement($locationId),
            'name' => $this->faker->randomElement($name),
            'e_kiosk_id'=>$this->faker->randomElement($name),
            'status' => $this->faker->randomElement([0, 1]),
        ];
    }
}
