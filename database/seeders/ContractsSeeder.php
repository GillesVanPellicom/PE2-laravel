<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContractsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('contracts')->insert([
            ['employee_id' => 1, 'job_id' => 1, 'start_date' => '2024-03-01', 'end_date' => null, 'status' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 2, 'job_id' => 2, 'start_date' => '2024-03-02', 'end_date' => null, 'status' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 3, 'job_id' => 3, 'start_date' => '2024-03-03', 'end_date' => null, 'status' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 4, 'job_id' => 4, 'start_date' => '2024-03-04', 'end_date' => null, 'status' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 5, 'job_id' => 5, 'start_date' => '2024-03-05', 'end_date' => null, 'status' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 6, 'job_id' => 1, 'start_date' => '2024-03-06', 'end_date' => null, 'status' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 7, 'job_id' => 2, 'start_date' => '2024-03-07', 'end_date' => null, 'status' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 8, 'job_id' => 3, 'start_date' => '2024-03-08', 'end_date' => null, 'status' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 9, 'job_id' => 4, 'start_date' => '2024-03-09', 'end_date' => null, 'status' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 10, 'job_id' => 5, 'start_date' => '2024-03-10', 'end_date' => null, 'status' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 11, 'job_id' => 1, 'start_date' => '2024-03-11', 'end_date' => null, 'status' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
