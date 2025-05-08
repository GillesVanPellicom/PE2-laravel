<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EmployeeContract;
use App\Models\EmployeeFunction;
use App\Models\Location;
use App\Models\Employee;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = EmployeeContract::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 year');
        $endDate = $this->faker->optional()->dateTimeBetween($startDate, '+2 years');

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'employee_id' => \App\Models\Employee::inRandomOrder()->first()->id,
            'job_id' => \App\Models\EmployeeFunction::inRandomOrder()->first()->id,
            'location_id' => \App\Models\Location::inRandomOrder()->first()->id,
        ];
    }
}
