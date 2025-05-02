<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AirportsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('airports')->insert([
            ['location_id' => 1, 'name' => 'EBBR', 'city_id' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Brussels Airport
            ['location_id' => 2, 'name' => 'EHAM', 'city_id' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Amsterdam Airport Schiphol
            ['location_id' => 3, 'name' => 'BER', 'city_id' => 9, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Berlin Tegel Airport
            ['location_id' => 4, 'name' => 'EBAW', 'city_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Antwerp International Airport
            ['location_id' => 5, 'name' => 'KLAX', 'city_id' => 11, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Los Angeles International Airport
            ['location_id' => 6, 'name' => 'EBCI', 'city_id' => 14, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Brussels South Charleroi Airport
            ['location_id' => 7, 'name' => 'EDDM', 'city_id' => 12, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Munich International Airport
            ['location_id' => 8, 'name' => 'EGKK', 'city_id' => 5, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // London Gatwick Airport
            ['location_id' => 9, 'name' => 'EPWA', 'city_id' => 13, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Warsaw Chopin Airport
            ['location_id' => 10, 'name' => 'EHAM', 'city_id' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Amsterdam Airport Schiphol
            ['location_id' => 11, 'name' => 'EHEH', 'city_id' => 19, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Eindhoven Airport
            ['location_id' => 12, 'name' => 'LEMD', 'city_id' => 7, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Adolfo Suarez Madrid-Barajas Airport
            ['location_id' => 13, 'name' => 'LEBL', 'city_id' => 8, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Barcelona El Prat Airport
            ['location_id' => 14, 'name' => 'LEMG', 'city_id' => 20, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Malaga Airport
            ['location_id' => 15, 'name' => 'EDDT', 'city_id' => 9, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Berlin Tegel Airport
            ['location_id' => 16, 'name' => 'EDDL', 'city_id' => 21, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Dusseldorf Airport
            ['location_id' => 17, 'name' => 'EPKT', 'city_id' => 22, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Katowice International Airport
            ['location_id' => 18, 'name' => 'EPPO', 'city_id' => 23, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Poznań–Ławica Henryk Wieniawski Airport
            ['location_id' => 19, 'name' => 'LPPT', 'city_id' => 24, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Lisbon Portela Airport
            ['location_id' => 20, 'name' => 'LPPR', 'city_id' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Francisco Sá Carneiro Airport
            ['location_id' => 21, 'name' => 'LIRF', 'city_id' => 26, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Rome–Fiumicino International Airport
            ['location_id' => 22, 'name' => 'LIML', 'city_id' => 27, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Milan-Linate Airport
            ['location_id' => 23, 'name' => 'LGAV', 'city_id' => 28, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Athens International Airport
            ['location_id' => 24, 'name' => 'LGIR', 'city_id' => 29, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Heraklion International Airport
            ['location_id' => 25, 'name' => 'EGCC', 'city_id' => 6, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Manchester Airport
            ['location_id' => 26, 'name' => 'EKCH', 'city_id' => 29, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Copenhagen Airport
            ['location_id' => 27, 'name' => 'EKBI', 'city_id' => 30, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Billund Airport
            ['location_id' => 28, 'name' => 'EKYT', 'city_id' => 31, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Aalborg Airport
            ['location_id' => 29, 'name' => 'ENGM', 'city_id' => 32, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Oslo Airport
            ['location_id' => 30, 'name' => 'ESSB', 'city_id' => 33, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Stockholm Bromma Airport
            ['location_id' => 31, 'name' => 'EFHK', 'city_id' => 34, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Helsinki-Vantaa Airport
        ]);
    }
}
