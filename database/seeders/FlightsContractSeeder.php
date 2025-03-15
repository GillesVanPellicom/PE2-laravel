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
            'created_at' => Carbon::now(), 
            'updated_at' => Carbon::now()]
        ]);
    }
}