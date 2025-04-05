<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(3),
            'location_type' => $this->faker->randomElement(['ADDRESS']),
            'addresses_id' => Address::factory(),
            'contact_number' => $this->faker->phoneNumber(),
            'opening_hours' => '08:00 - 18:00',
            'is_active' => $this->faker->boolean(),
            'latitude' => $this->faker->latitude(51.40, 49.72),
            'longitude' => $this->faker->longitude(2.7, 6.33),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
