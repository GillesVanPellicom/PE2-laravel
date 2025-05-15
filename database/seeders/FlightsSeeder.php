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
            'departure_time'=>Carbon::now()->addHours(1),
            'time_flight_minutes'=>480,
            'departure_day_of_week'=>'Monday',
            'departure_date'=>Carbon::now(),
            'status'=>'On Time',
            'isActive' => 1,
            'gate' => 'A1',
            'router_edge_id' => 1,
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
            'router_edge_id' => 2,
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
            'router_edge_id' => 3,
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
            'router_edge_id' => 4,
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
                'router_edge_id' => 5,
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
                'departure_date' => Carbon::now(),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'F6',
                'router_edge_id' => 6,
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
                'departure_date' => Carbon::now(),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'G7',
                'router_edge_id' => 7,
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
                'departure_date' => Carbon::now(),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'H8',
                'router_edge_id' => 8,
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
                'router_edge_id' => 9,
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
                'router_edge_id' => 10,
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
                'router_edge_id' => 11,
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
                'router_edge_id' => 51,
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
                'router_edge_id' => 51,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 8,
                'depart_location_id' => 1,
                'arrive_location_id' => 12, 
                'departure_time' => Carbon::now()->addHours(6),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Friday', 
                'departure_date' => Carbon::now()->addDays(4),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'H8',
                'router_edge_id' => 29,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 8,
                'depart_location_id' => 1,
                'arrive_location_id' => 12, 
                'departure_time' => Carbon::now(),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Monday', 
                'departure_date' => Carbon::now(),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'H8',
                'router_edge_id' => 29,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 8,
                'depart_location_id' => 4,
                'arrive_location_id' => 5, 
                'departure_time' => Carbon::now(),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Monday', 
                'departure_date' => Carbon::now(),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'H8',
                'router_edge_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 8,
                'depart_location_id' => 5,
                'arrive_location_id' => 4, 
                'departure_time' => Carbon::now()->addDays(1),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Tuesday', 
                'departure_date' => Carbon::now()->addDays(1),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'H8',
                'router_edge_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 8,
                'depart_location_id' => 5,
                'arrive_location_id' => 4, 
                'departure_time' => Carbon::now()->addDays(2),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Wednesday', 
                'departure_date' => Carbon::now()->addDays(2),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'H8',
                'router_edge_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 9,
                'depart_location_id' => 4,
                'arrive_location_id' => 5,
                'departure_time' => Carbon::now()->addDays(3)->setTime(10, 0),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Thursday',
                'departure_date' => Carbon::now()->addDays(3),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'I9',
                'router_edge_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 10,
                'depart_location_id' => 5,
                'arrive_location_id' => 4,
                'departure_time' => Carbon::now()->addDays(4)->setTime(14, 0),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Friday',
                'departure_date' => Carbon::now()->addDays(4),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'J10',
                'router_edge_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 11,
                'depart_location_id' => 4,
                'arrive_location_id' => 5,
                'departure_time' => Carbon::now()->addDays(5)->setTime(16, 0),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Saturday',
                'departure_date' => Carbon::now()->addDays(5),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'K11',
                'router_edge_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 12,
                'depart_location_id' => 5,
                'arrive_location_id' => 4,
                'departure_time' => Carbon::now()->addDays(6)->setTime(18, 0),
                'time_flight_minutes' => 240,
                'departure_day_of_week' => 'Sunday',
                'departure_date' => Carbon::now()->addDays(6),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'L12',
                'router_edge_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 13,
                'depart_location_id' => 4, // EBAW (Antwerp)
                'arrive_location_id' => 5, // KLAX (Los Angeles)
                'departure_time' => Carbon::now()->addDays(7)->setTime(9, 0),
                'time_flight_minutes' => 720,
                'departure_day_of_week' => 'Monday',
                'departure_date' => Carbon::now()->addDays(7),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'M13',
                'router_edge_id' => 7, // @AIR_EBAW <-> @AIR_KLAX
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 14,
                'depart_location_id' => 5, // KLAX (Los Angeles)
                'arrive_location_id' => 4, // EBAW (Antwerp)
                'departure_time' => Carbon::now()->addDays(8)->setTime(11, 0),
                'time_flight_minutes' => 720,
                'departure_day_of_week' => 'Tuesday',
                'departure_date' => Carbon::now()->addDays(8),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'N14',
                'router_edge_id' => 7, // @AIR_KLAX <-> @AIR_EBAW
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 15,
                'depart_location_id' => 1, // EBBR (Brussels)
                'arrive_location_id' => 9, // EPWA (Warsaw)
                'departure_time' => Carbon::now()->addDays(9)->setTime(13, 0),
                'time_flight_minutes' => 150,
                'departure_day_of_week' => 'Wednesday',
                'departure_date' => Carbon::now()->addDays(9),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'O15',
                'router_edge_id' => 15, // @AIR_EBBR <-> @AIR_EPWA
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 16,
                'depart_location_id' => 9, // EPWA (Warsaw)
                'arrive_location_id' => 1, // EBBR (Brussels)
                'departure_time' => Carbon::now()->addDays(10)->setTime(15, 0),
                'time_flight_minutes' => 150,
                'departure_day_of_week' => 'Thursday',
                'departure_date' => Carbon::now()->addDays(10),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'P16',
                'router_edge_id' => 15, // @AIR_EPWA <-> @AIR_EBBR
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 17,
                'depart_location_id' => 1, // EBBR (Brussels)
                'arrive_location_id' => 7, // EDDM (Munich)
                'departure_time' => Carbon::now()->addDays(11)->setTime(17, 0),
                'time_flight_minutes' => 120,
                'departure_day_of_week' => 'Friday',
                'departure_date' => Carbon::now()->addDays(11),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'Q17',
                'router_edge_id' => 17, // @AIR_EBBR <-> @AIR_EDDM
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 18,
                'depart_location_id' => 7, // EDDM (Munich)
                'arrive_location_id' => 1, // EBBR (Brussels)
                'departure_time' => Carbon::now()->addDays(12)->setTime(19, 0),
                'time_flight_minutes' => 120,
                'departure_day_of_week' => 'Saturday',
                'departure_date' => Carbon::now()->addDays(12),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'R18',
                'router_edge_id' => 17, // @AIR_EDDM <-> @AIR_EBBR
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 19,
                'depart_location_id' => 1, // EBBR (Brussels)
                'arrive_location_id' => 12, // LEMD (Madrid)
                'departure_time' => Carbon::now()->addDays(13)->setTime(8, 0),
                'time_flight_minutes' => 160,
                'departure_day_of_week' => 'Sunday',
                'departure_date' => Carbon::now()->addDays(13),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'S19',
                'router_edge_id' => 27, // @AIR_EBBR <-> @AIR_LEMD
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 20,
                'depart_location_id' => 12, // LEMD (Madrid)
                'arrive_location_id' => 1, // EBBR (Brussels)
                'departure_time' => Carbon::now()->addDays(14)->setTime(10, 0),
                'time_flight_minutes' => 160,
                'departure_day_of_week' => 'Monday',
                'departure_date' => Carbon::now()->addDays(14),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'T20',
                'router_edge_id' => 27, // @AIR_LEMD <-> @AIR_EBBR
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 21,
                'depart_location_id' => 1, // EBBR (Brussels)
                'arrive_location_id' => 22, // LIML (Milan)
                'departure_time' => Carbon::now()->addDays(15)->setTime(12, 0),
                'time_flight_minutes' => 120,
                'departure_day_of_week' => 'Tuesday',
                'departure_date' => Carbon::now()->addDays(15),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'U21',
                'router_edge_id' => 41, // @AIR_EBBR <-> @AIR_LIML
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 22,
                'depart_location_id' => 22, // LIML (Milan)
                'arrive_location_id' => 1, // EBBR (Brussels)
                'departure_time' => Carbon::now()->addDays(16)->setTime(14, 0),
                'time_flight_minutes' => 120,
                'departure_day_of_week' => 'Wednesday',
                'departure_date' => Carbon::now()->addDays(16),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'V22',
                'router_edge_id' => 41, // @AIR_LIML <-> @AIR_EBBR
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 23,
                'depart_location_id' => 1, // EBBR (Brussels)
                'arrive_location_id' => 23, // LGAV (Athens)
                'departure_time' => Carbon::now()->addDays(17)->setTime(16, 0),
                'time_flight_minutes' => 200,
                'departure_day_of_week' => 'Thursday',
                'departure_date' => Carbon::now()->addDays(17),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'W23',
                'router_edge_id' => 49, // @AIR_EBBR <-> @AIR_LGAV
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 24,
                'depart_location_id' => 23, // LGAV (Athens)
                'arrive_location_id' => 1, // EBBR (Brussels)
                'departure_time' => Carbon::now()->addDays(18)->setTime(18, 0),
                'time_flight_minutes' => 200,
                'departure_day_of_week' => 'Friday',
                'departure_date' => Carbon::now()->addDays(18),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'X24',
                'router_edge_id' => 49, // @AIR_LGAV <-> @AIR_EBBR
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 25,
                'depart_location_id' => 1, // EBBR (Brussels)
                'arrive_location_id' => 19, // LPPT (Lisbon)
                'departure_time' => Carbon::now()->addDays(19)->setTime(7, 0),
                'time_flight_minutes' => 180,
                'departure_day_of_week' => 'Saturday',
                'departure_date' => Carbon::now()->addDays(19),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'Y25',
                'router_edge_id' => 37, // @AIR_EBBR <-> @AIR_LPPT
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 26,
                'depart_location_id' => 19, // LPPT (Lisbon)
                'arrive_location_id' => 1, // EBBR (Brussels)
                'departure_time' => Carbon::now()->addDays(20)->setTime(9, 0),
                'time_flight_minutes' => 180,
                'departure_day_of_week' => 'Sunday',
                'departure_date' => Carbon::now()->addDays(20),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'Z26',
                'router_edge_id' => 37, // @AIR_LPPT <-> @AIR_EBBR
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 27,
                'depart_location_id' => 1, // EBBR (Brussels)
                'arrive_location_id' => 31, // EFHK (Helsinki)
                'departure_time' => Carbon::now()->addDays(21)->setTime(11, 0),
                'time_flight_minutes' => 160,
                'departure_day_of_week' => 'Monday',
                'departure_date' => Carbon::now()->addDays(21),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'AA27',
                'router_edge_id' => 67, // @AIR_EBBR <-> @AIR_EFHK
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 28,
                'depart_location_id' => 31, // EFHK (Helsinki)
                'arrive_location_id' => 1, // EBBR (Brussels)
                'departure_time' => Carbon::now()->addDays(22)->setTime(13, 0),
                'time_flight_minutes' => 160,
                'departure_day_of_week' => 'Tuesday',
                'departure_date' => Carbon::now()->addDays(22),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'BB28',
                'router_edge_id' => 67, // @AIR_EFHK <-> @AIR_EBBR
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 29,
                'depart_location_id' => 1, // EBBR (Brussels)
                'arrive_location_id' => 30, // ESSB (Stockholm)
                'departure_time' => Carbon::now()->addDays(23)->setTime(15, 0),
                'time_flight_minutes' => 140,
                'departure_day_of_week' => 'Wednesday',
                'departure_date' => Carbon::now()->addDays(23),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'CC29',
                'router_edge_id' => 65, // @AIR_EBBR <-> @AIR_ESSB
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 30,
                'depart_location_id' => 30, // ESSB (Stockholm)
                'arrive_location_id' => 1, // EBBR (Brussels)
                'departure_time' => Carbon::now()->addDays(24)->setTime(17, 0),
                'time_flight_minutes' => 140,
                'departure_day_of_week' => 'Thursday',
                'departure_date' => Carbon::now()->addDays(24),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'DD30',
                'router_edge_id' => 65, // @AIR_ESSB <-> @AIR_EBBR
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 31,
                'depart_location_id' => 5, // KLAX (Los Angeles)
                'arrive_location_id' => 4, // EBAW (Antwerp)
                'departure_time' => Carbon::now()->addDays(25)->setTime(10, 0),
                'time_flight_minutes' => 720,
                'departure_day_of_week' => 'Friday',
                'departure_date' => Carbon::now()->addDays(25),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'EE31',
                'router_edge_id' => 7, // @AIR_KLAX <-> @AIR_EBAW
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 32,
                'depart_location_id' => 5, // KLAX (Los Angeles)
                'arrive_location_id' => 4, // EBAW (Antwerp)
                'departure_time' => Carbon::now()->addDays(26)->setTime(14, 0),
                'time_flight_minutes' => 720,
                'departure_day_of_week' => 'Friday',
                'departure_date' => Carbon::now()->addDays(26),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'FF32',
                'router_edge_id' => 7, // @AIR_KLAX <-> @AIR_EBAW
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 33,
                'depart_location_id' => 5, // KLAX (Los Angeles)
                'arrive_location_id' => 4, // EBAW (Antwerp)
                'departure_time' => Carbon::now()->addDays(27)->setTime(18, 0),
                'time_flight_minutes' => 720,
                'departure_day_of_week' => 'Friday',
                'departure_date' => Carbon::now()->addDays(27),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'GG33',
                'router_edge_id' => 7, // @AIR_KLAX <-> @AIR_EBAW
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 34,
                'depart_location_id' => 4, // EBAW (Antwerp)
                'arrive_location_id' => 5, // KLAX (Los Angeles)
                'departure_time' => Carbon::now()->addDays(28)->setTime(8, 0),
                'time_flight_minutes' => 720,
                'departure_day_of_week' => 'Friday',
                'departure_date' => Carbon::now()->addDays(28),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'HH34',
                'router_edge_id' => 7, // @AIR_EBAW <-> @AIR_KLAX
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 35,
                'depart_location_id' => 4, // EBAW (Antwerp)
                'arrive_location_id' => 5, // KLAX (Los Angeles)
                'departure_time' => Carbon::now()->addDays(29)->setTime(12, 0),
                'time_flight_minutes' => 720,
                'departure_day_of_week' => 'Friday',
                'departure_date' => Carbon::now()->addDays(29),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'II35',
                'router_edge_id' => 7, // @AIR_EBAW <-> @AIR_KLAX
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'airplane_id' => 36,
                'depart_location_id' => 4, // EBAW (Antwerp)
                'arrive_location_id' => 5, // KLAX (Los Angeles)
                'departure_time' => Carbon::now()->addDays(30)->setTime(16, 0),
                'time_flight_minutes' => 720,
                'departure_day_of_week' => 'Friday',
                'departure_date' => Carbon::now()->addDays(30),
                'status' => 'On Time',
                'isActive' => 1,
                'gate' => 'JJ36',
                'router_edge_id' => 7, // @AIR_EBAW <-> @AIR_KLAX
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}
