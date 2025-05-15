<?php

namespace Database\Factories;

use App\Models\EmployeeFunction;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FunctionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeFunction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->jobTitle,
            'role' => Role::inRandomOrder()->first()->name,
            'description' => $this->faker->sentence,
            'salary_min' => $this->faker->numberBetween(3000, 5000),
            'salary_max' => $this->faker->numberBetween(5000, 10000),
        ];
    }
}
