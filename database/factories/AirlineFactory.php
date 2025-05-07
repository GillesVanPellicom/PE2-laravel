<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AirlineFactory extends Factory
{
    public function definition(): array
    {

        return [
            'name' => $this->faker->company,
            'IATA_code' => strtoupper($this->faker->unique()->lexify('??')),
            'contact_number' => $this->faker->phoneNumber,
            'headquarters_location' => $this->faker->city,
        ];
    }
}
