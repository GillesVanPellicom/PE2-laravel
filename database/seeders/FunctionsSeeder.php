<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FunctionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('functions')->insert([
            [
                'name' => 'Courier',
                'role' => 'courier',
                'description' => 'Responsible for delivering packages to customers.',
                'salary_min' => 2000.00,
                'salary_max' => 3000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pilot',
                'role' => 'employee',
                'description' => 'Operates the aircraft for transporting packages.',
                'salary_min' => 4000.00,
                'salary_max' => 7000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Warehouse Manager',
                'role' => 'employee',
                'description' => 'Manages the distribution center operations.',
                'salary_min' => 3000.00,
                'salary_max' => 5000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Customer Service Agent',
                'role' => 'employee',
                'description' => 'Assists customers with inquiries and complaints.',
                'salary_min' => 1800.00,
                'salary_max' => 2500.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Driver',
                'role' => 'employee',
                'description' => 'Drives vans or trucks to transport packages between locations.',
                'salary_min' => 2200.00,
                'salary_max' => 3500.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Manager HR',
                'role' => 'HRManager',
                'description' => 'Manages the human resources department.',
                'salary_min' => 4000.00,
                'salary_max' => 6000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'HR Employee',
                'role' => 'HR',
                'description' => 'Assists the HR Manager with administrative tasks.',
                'salary_min' => 2000.00,
                'salary_max' => 3000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pickup point employee',
                'role' => 'pickup',
                'description' => 'employee of a pickup point',
                'salary_min' => 4000.00,
                'salary_max' => 6000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Distribution Center Manager',
                'role' => 'DCManager',
                'description' => 'Manages the distribution center operations.',
                'salary_min' => 4000.00,
                'salary_max' => 6000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
