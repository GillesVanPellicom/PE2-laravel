<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeamsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('teams')->insert([
            [
                'department' => 'Logistics',
                'manager_id' => 1, // Jordi
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'department' => 'IT',
                'manager_id' => 1, // Szymon
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'department' => 'HR',
                'manager_id' => 1, // Thomas
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'department' => 'Finance',
                'manager_id' => 1, // Julien
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'department' => 'Operations',
                'manager_id' => 1, // Keith
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}