<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('employees')->insert([
            ['leave_balance' => 25,'user_id' => 1, 'team_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['leave_balance' => 25,'user_id' => 3, 'team_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            
        ]);
    }
}
