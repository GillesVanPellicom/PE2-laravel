<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        // Gedefinieerde steden ophalen uit de database
        $cityIds = City::pluck('id')->toArray();

        return [
            'street' => $this->faker->streetName(),
            'house_number' => $this->faker->buildingNumber(),
            'bus_number' => $this->faker->optional()->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']), // Soms een busnummer genereren
            'cities_id' => $this->faker->randomElement($cityIds), // Alleen bestaande steden gebruiken
        ];
    }
}