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
            ['leave_balance' => 25,'user_id' => 3, 'team_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['leave_balance' => 25,'user_id' => 6, 'team_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['leave_balance' => 25,'user_id' => 7, 'team_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['leave_balance' => 25,'user_id' => 8, 'team_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['leave_balance' => 25,'user_id' => 9, 'team_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['leave_balance' => 25,'user_id' => 10, 'team_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['leave_balance' => 25,'user_id' => 11, 'team_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['leave_balance' => 25,'user_id' => 12, 'team_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['leave_balance' => 25,'user_id' => 14, 'team_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        \App\Models\Team::factory(10)->create();

        // Ensure unique user_id values for employees with user_id > 50
        $userIds = \App\Models\User::where('id', '>', 50)->doesntHave('employee')->pluck('id')->shuffle()->take(50);
        foreach ($userIds as $userId) {
            \App\Models\Employee::factory()->create(['user_id' => $userId]);
        }

        \App\Models\Team::factory(20)->create();

    }
}
