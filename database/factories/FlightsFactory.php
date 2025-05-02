<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FlightsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'flight_number' => $this->faker->unique()->bothify('??###'),
            'departure_time' => $this->faker->dateTimeBetween('now', '+1 year'),
            'arrival_time' => $this->faker->dateTimeBetween('+1 hour', '+2 years'),
            'origin' => $this->faker->city,
            'destination' => $this->faker->city,
            'airline' => $this->faker->company,
            'status' => $this->faker->randomElement(['on time', 'delayed', 'cancelled']),
        ];
    }
}
