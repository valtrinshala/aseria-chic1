<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class PaymentMethodFactory extends Factory
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function definition(): array
    {

       
        $name = [
            "Cash",
            "Card",
            "pix",
          ];

        return [
            'id' => Uuid::uuid4(),
            'name' => $this->faker->randomElement($name),
        ];
    }
}
