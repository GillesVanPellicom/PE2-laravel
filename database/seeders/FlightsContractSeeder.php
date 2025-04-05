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
            ["flight_id"=>1,
            "airline_id"=>1,
            "max_capacity"=>65,
            "price"=>2000,
            "start_date"=>'2024-03-01',
            'created_at' => Carbon::now(), 
            'updated_at' => Carbon::now()]
        ]);
    }
}