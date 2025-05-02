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
        if ($employee->leave_balance < 5) {
            $days = $this->faker->numberBetween(1, $employee->leave_balance);
            $employee->leave_balance -= $days;
            $employee->save();
        } else {
            $days = $this->faker->numberBetween(1, 5);
            $employee->leave_balance -= $days;
            $employee->save();
        }

        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 year');
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDate = (clone $startDate)->add(new \DateInterval("P{$days}D"));

        return [
            'employee_id' => $employee->id,
            'start_date' => $startDateFormatted,
            'end_date' => $endDate->format('Y-m-d'),
            'vacation_type' => $this->faker->randomElement(['Paid Leave', 'Sick Leave']),
            'day_type' => $this->faker->randomElement(['Whole Day', 'First Half', 'Second Half']),
            'approve_status' => $this->faker->randomElement(['approved', 'pending', 'rejected']),
        ];
    }
}
