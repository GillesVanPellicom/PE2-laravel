<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FlightsContractSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('_flight__contracts')->insert([
            [
                "flight_id" => 1,
                "airline_id" => 1,
                "max_capacity" => 100,
                "price" => 2000,
                "start_date" => '2024-03-01',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                "flight_id" => 2,
                "airline_id" => 1,
                "max_capacity" => 100,
                "price" => 2000,
                "start_date" => '2024-03-01',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                "flight_id" => 3,
                "airline_id" => 1,
                "max_capacity" => 100,
                "price" => 2000,
                "start_date" => '2024-03-01',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                "flight_id" => 4,
                "airline_id" => 1,
                "max_capacity" => 100,
                "price" => 2000,
                "start_date" => '2024-03-01',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                "flight_id" => 12,
                "airline_id" => 1,
                "max_capacity" => 100,
                "price" => 2000,
                "start_date" => '2024-03-01',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                "flight_id" => 13,
                "airline_id" => 1,
                "max_capacity" => 100,
                "price" => 2000,
                "start_date" => '2024-03-01',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                "flight_id" => 14,
                "airline_id" => 1,
                "max_capacity" => 100,
                "price" => 2000,
                "start_date" => '2024-03-01',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}