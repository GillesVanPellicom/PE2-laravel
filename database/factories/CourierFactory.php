<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourierFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'birth_date' => $this->faker->dateTimeBetween('-40 years', '-20 years'),
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'address_id' => Address::factory()
        ];
    }
}