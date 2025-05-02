<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vacation>
 */
class VacationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $employee = \App\Models\Employee::inRandomOrder()->first();
        $type = $this->faker->randomElement(['Holiday', 'Sick Leave']);

        if($type == 'Holiday') {
            $employee->leave_balance --;
            $employee->save();
        }

        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $startDateFormatted = $startDate->format('Y-m-d');

        return [
            'employee_id' => $employee->id,
            'start_date' => $startDateFormatted,
            'end_date' => $startDateFormatted,
            'vacation_type' => $this->faker->randomElement(['Holiday', 'Sick Leave']),
            'day_type' => $this->faker->randomElement(['Whole Day', 'First Half', 'Second Half']),
            'approve_status' => $this->faker->randomElement(array_merge(
                array_fill(0, 70, 'approved'),
                ['pending', 'rejected']
            )),
        ];
    }
}
