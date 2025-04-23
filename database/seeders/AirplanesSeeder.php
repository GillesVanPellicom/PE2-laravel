<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AirplanesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('airplanes')->insert([
            [
                'airline_id' => 1, // Brussels Airlines
                'model' => 'Boeing 747 Cargo',
                'capacity' => 112760, // KG
                'status' => 'Active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'airline_id' => 2, // KLM
                'model' => 'Airbus A330 Cargo',
                'capacity' => 70000, // KG
                'status' => 'Active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'airline_id' => 3, // Lufthansa
                'model' => 'Boeing 777F',
                'capacity' => 102010, // KG
                'status' => 'Active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'airline_id' => 4, // Ryanair
                'model' => 'Boeing 737 Cargo',
                'capacity' => 20000, // KG
                'status' => 'Inactive',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'airline_id' => 5, // Air France
                'model' => 'Airbus A300 Cargo',
                'capacity' => 43000, // KG
                'status' => 'Active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'airline_id' => 5, // Emirates
                'model' => 'Boeing 777 Cargo',
                'capacity' => 103000, // KG
                'status' => 'Active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'airline_id' => 5, // Qatar Airways
                'model' => 'Airbus A350 Cargo',
                'capacity' => 97000, // KG
                'status' => 'Active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'airline_id' => 5, // Turkish Airlines
                'model' => 'Boeing 787 Cargo',
                'capacity' => 110000, // KG
                'status' => 'Active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
