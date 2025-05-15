<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehiclesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vehicles')->insert([
            [
                'current_location_id' => 1, // Pickup Point 1
                'vehicle_type' => 'Van',
                'license_plate' => '1-ABC-123',
                'capacity' => 1000, // in KG
                'status' => 'Available',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'current_location_id' => 1, // Distribution Center 1
                'vehicle_type' => 'Truck',
                'license_plate' => '2-XYZ-456',
                'capacity' => 5000,
                'status' => 'Available',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'current_location_id' => 1, // Airport 1
                'vehicle_type' => 'Van',
                'license_plate' => '3-QWE-789',
                'capacity' => 800,
                'status' => 'In Use',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'current_location_id' => 1, // Distribution Center 2
                'vehicle_type' => 'Truck',
                'license_plate' => '4-RTY-654',
                'capacity' => 4500,
                'status' => 'In Use',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'current_location_id' => 1, // Pickup Point 2
                'vehicle_type' => 'Van',
                'license_plate' => '5-FGH-321',
                'capacity' => 1200,
                'status' => 'Available',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
