<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Helpers\ConsoleHelper;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contractStartTime = microtime(true);
        ConsoleHelper::task(str_pad("[User]", 10, ' ', STR_PAD_RIGHT)." Creating user: ".$this->faker->firstName().' '.$this->faker->lastName(),
            function () {});

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'isCompany' => $isCompany = fake()->boolean(),
            'company_name' => $isCompany ? fake()->company() : null,
            'vat_number' => $isCompany ? fake()->regexify('[A-Z]{2}[0-9]{9}') : null,
            'phone_number' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'address_id' => \App\Models\Address::inRandomOrder()->first()->id,
            'remember_token' => Str::random(10),
            'birth_date' => fake()->dateTimeBetween('-90 years', '-18 years'),
        ];
        $executionTimes[] = (microtime(true) - $contractStartTime) * 1000;
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
