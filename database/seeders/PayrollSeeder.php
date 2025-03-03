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
            ['employee_id' => 1, 'base_salary' => 3000, 'bonus' => 500, 'taxes' => 700, 'net_salary' => 2800, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 2, 'base_salary' => 2800, 'bonus' => 400, 'taxes' => 650, 'net_salary' => 2550, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 3, 'base_salary' => 2900, 'bonus' => 450, 'taxes' => 680, 'net_salary' => 2670, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 4, 'base_salary' => 2700, 'bonus' => 400, 'taxes' => 600, 'net_salary' => 2500, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 5, 'base_salary' => 3100, 'bonus' => 550, 'taxes' => 750, 'net_salary' => 2900, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 6, 'base_salary' => 2600, 'bonus' => 300, 'taxes' => 580, 'net_salary' => 2320, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 7, 'base_salary' => 2700, 'bonus' => 400, 'taxes' => 600, 'net_salary' => 2500, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 8, 'base_salary' => 2800, 'bonus' => 450, 'taxes' => 630, 'net_salary' => 2620, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 9, 'base_salary' => 2500, 'bonus' => 350, 'taxes' => 550, 'net_salary' => 2300, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 10, 'base_salary' => 2700, 'bonus' => 400, 'taxes' => 600, 'net_salary' => 2500, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['employee_id' => 11, 'base_salary' => 2600, 'bonus' => 300, 'taxes' => 580, 'net_salary' => 2320, 'payment_date' => Carbon::now(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
