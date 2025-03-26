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
            ['employee_id' => 1, 'job_id' => 6, 'start_date' => '2024-03-01', 'end_date' => null,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 2, 'job_id' => 7, 'start_date' => '2024-03-02', 'end_date' => null,  'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

        ]);

    }
}
