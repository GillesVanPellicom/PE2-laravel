<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('payrolls')->insert([
            ['user_id' => 1, 'base_salary' => 3000, 'bonus' => 500, 'taxes' => 700, 'net_salary' => 2800, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['user_id' => 3, 'base_salary' => 2800, 'bonus' => 400, 'taxes' => 650, 'net_salary' => 2550, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
