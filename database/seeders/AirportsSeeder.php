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
            ['location_id' => 1, 'name' => 'EBBR', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Brussels Airport
            ['location_id' => 2, 'name' => 'EHAM', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Amsterdam Airport Schiphol
            ['location_id' => 3, 'name' => 'BER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Berlin Tegel Airport
            ['location_id' => 4, 'name' => 'EBAW', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Antwerp International Airport
            ['location_id' => 5, 'name' => 'KLAX', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Los Angeles International Airport
            ['location_id' => 6, 'name' => 'EBCI', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Brussels South Charleroi Airport
            ['location_id' => 7, 'name' => 'EDDM', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Munich International Airport
            ['location_id' => 8, 'name' => 'EGKK', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // London Gatwick Airport
            ['location_id' => 9, 'name' => 'EPWA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Warsaw Chopin Airport
            ['location_id' => 10, 'name' => 'EHAM', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Amsterdam Airport Schiphol
            ['location_id' => 11, 'name' => 'EHEH', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Eindhoven Airport
            ['location_id' => 12, 'name' => 'LEMD', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Adolfo Suarez Madrid-Barajas Airport
            ['location_id' => 13, 'name' => 'LEBL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Barcelona El Prat Airport
            ['location_id' => 14, 'name' => 'LEMG', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Malaga Airport
            ['location_id' => 15, 'name' => 'EDDT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Berlin Tegel Airport
            ['location_id' => 16, 'name' => 'EDDL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Dusseldorf Airport
            ['location_id' => 17, 'name' => 'EPKT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Katowice International Airport
            ['location_id' => 18, 'name' => 'EPPO', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Poznań–Ławica Henryk Wieniawski Airport
            ['location_id' => 19, 'name' => 'LPPT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Lisbon Portela Airport
            ['location_id' => 20, 'name' => 'LPPR', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Francisco Sá Carneiro Airport
            ['location_id' => 21, 'name' => 'LIRF', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Rome–Fiumicino International Airport
            ['location_id' => 22, 'name' => 'LIML', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Milan-Linate Airport
            ['location_id' => 23, 'name' => 'LGAV', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Athens International Airport
            ['location_id' => 24, 'name' => 'LGIR', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Heraklion International Airport
            ['location_id' => 25, 'name' => 'EGCC', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Manchester Airport
            ['location_id' => 26, 'name' => 'EKCH', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Copenhagen Airport
            ['location_id' => 27, 'name' => 'EKBI', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Billund Airport
            ['location_id' => 28, 'name' => 'EKYT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Aalborg Airport
            ['location_id' => 29, 'name' => 'ENGM', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Oslo Airport
            ['location_id' => 30, 'name' => 'ESSB', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Stockholm Bromma Airport
            ['location_id' => 31, 'name' => 'EFHK', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Helsinki-Vantaa Airport
        ]);
    }
}
