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
            [
            'airplane_id' => 1,
            'depart_location_id'=>1,
            'arrive_location_id'=>1,
            'departure_time'=>Carbon::now(),
            'time_flight_minutes'=>480,
            'departure_day_of_week'=>'Monday',
            'departure_date'=>Carbon::now(),
            'status'=>'On Time',
            'isActive' => 1,
            'created_at' => Carbon::now(), 
            'updated_at' => Carbon::now()
            ],
            [
            'airplane_id' => 2,
            'depart_location_id'=>2,
            'arrive_location_id'=>1,
            'departure_time'=>Carbon::now(),
            'time_flight_minutes'=>120,
            'departure_day_of_week'=>'Wednesday',
            'departure_date'=>Carbon::now(),
            'status'=>'On Time',
            'isActive' => 1,
            'created_at' => Carbon::now(), 
            'updated_at' => Carbon::now()
            ],
            [
            'airplane_id' => 3,
            'depart_location_id'=>2,
            'arrive_location_id'=>1,
            'departure_time'=>Carbon::now(),
            'time_flight_minutes'=>480,
            'departure_day_of_week'=>'Friday',
            'departure_date'=>Carbon::now(),
            'status'=>'On Time',
            'isActive' => 1,
            'created_at' => Carbon::now(), 
            'updated_at' => Carbon::now()
            ],
            [
            'airplane_id' => 4,
            'depart_location_id' => 1,
            'arrive_location_id' => 2,
            'departure_time' => Carbon::now()->setTime(19, 0), // Ensure correct time is set
            'time_flight_minutes' => 250,
            'departure_day_of_week' => 'Friday',
            'departure_date' => Carbon::now(),
            'status' => 'On Time',
            'isActive' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ]
        ]);
    }
}
