<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'leave_balance' => $this->faker->numberBetween(10, 35),
            'team_id' => \App\Models\Team::inRandomOrder()->first()->id,
            'user_id' => \App\Models\User::doesntHave('employee')->inRandomOrder()->firstOrFail()->id,
        ];
    }
}
