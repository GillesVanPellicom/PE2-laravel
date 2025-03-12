<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FlightsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('flights')->insert([
            ['airplane_id' => 1,
            'depart_location_id'=>1,
            'arrive_location_id'=>1,
            'departure_time'=>Carbon::now(),
            'time_flight_minutes'=>480,
            'departure_day_of_week'=>'Monday',
            'departure_date'=>Carbon::now(),
            'status'=>'On Time',
            'created_at' => Carbon::now(), 
            'updated_at' => Carbon::now()]
        ]);
    }
}
