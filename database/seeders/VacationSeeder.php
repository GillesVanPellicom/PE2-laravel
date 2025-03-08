<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vacation;

class VacationSeeder extends Seeder
{
    public function run()
    {
        Vacation::insert([
            [
                'employee_id' => 1, // Assuming an employee exists
                'vacation_type' => 'Paid Leave',
                'start_date' => '2024-07-10',
                'end_date' => '2024-07-15',
                'approve_status' => 'Approved',
            ],
            [
                'employee_id' => 2,
                'vacation_type' => 'Sick Leave',
                'start_date' => '2024-08-01',
                'end_date' => '2024-08-03',
                'approve_status' => 'Pending',
            ],
        ]);
    }
}
