<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\Team::class;

    public function definition(): array
    {

        return [
            'department' => $this->faker->unique()->jobTitle,
            'manager_id' => \App\Models\Employee::inRandomOrder()->first()->id,
        ];
    }
}
