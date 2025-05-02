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
            'gate' => 'A1',
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
            'gate' => 'B2',
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
            'gate' => 'C3',
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
            'gate' => 'D4',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 5,
                'depart_location_id' => 1, // Example: '@AIR_EBBR'
                'arrive_location_id' => 2, // Example: '@AIR_EPWA'
                'departure_time' => Carbon::now()->addHours(7),
                'time_flight_minutes' => 120,
                'departure_day_of_week' => 'Tuesday',
                'departure_date' => Carbon::now()->addDays(1),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'E5',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'airplane_id' => 6,
                'depart_location_id' => 2, // Example: '@AIR_EPWA'
                'arrive_location_id' => 3, // Example: '@AIR_EDDM'
                'departure_time' => Carbon::now()->addHours(7),
                'time_flight_minutes' => 180,
                'departure_day_of_week' => 'Wednesday',
                'departure_date' => Carbon::now()->addDays(7),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'F6',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'airplane_id' => 7,
                'depart_location_id' => 3, // Example: '@AIR_EDDM'
                'arrive_location_id' => 4, // Example: '@AIR_EBCI'
                'departure_time' => Carbon::now()->addHours(6),
                'time_flight_minutes' => 90,
                'departure_day_of_week' => 'Thursday',
                'departure_date' => Carbon::now()->addDays(3),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'G7',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'airplane_id' => 8,
                'depart_location_id' => 4, // Example: '@AIR_EBCI'
                'arrive_location_id' => 5, // Example: '@AIR_LPPT'
                'departure_time' => Carbon::now()->addHours(8),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Friday',
                'departure_date' => Carbon::now()->addDays(4),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'H8',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'airplane_id' => 3,
                'depart_location_id' => 2,
                'arrive_location_id' => 1,
                'departure_time' => Carbon::now(),
                'time_flight_minutes' => 480,
                'departure_day_of_week' => 'Thursday', // Duplicate for Thursday
                'departure_date' => Carbon::now(),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'C3',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 4,
                'depart_location_id' => 1,
                'arrive_location_id' => 2,
                'departure_time' => Carbon::now()->setTime(19, 0),
                'time_flight_minutes' => 250,
                'departure_day_of_week' => 'Thursday', // Duplicate for Thursday
                'departure_date' => Carbon::now(),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'D4',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 8,
                'depart_location_id' => 4,
                'arrive_location_id' => 5,
                'departure_time' => Carbon::now()->addHours(8),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Thursday', // Duplicate for Thursday
                'departure_date' => Carbon::now()->addDays(4),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'H8',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 8,
                'depart_location_id' => 1,
                'arrive_location_id' => 23, 
                'departure_time' => Carbon::now()->addHours(8),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Friday', 
                'departure_date' => Carbon::now()->addDays(4),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'H8',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 8,
                'depart_location_id' => 1,
                'arrive_location_id' => 23, 
                'departure_time' => Carbon::now()->addHours(6),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Friday', 
                'departure_date' => Carbon::now()->addDays(4),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'H8',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}
