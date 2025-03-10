<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageMovementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('package_movements')->insert([
            [
                'package_id' => 1, 
                'from_location_id' => 1, 
                'to_location_id' => 2, 
                'handled_by_courier_id' => null, 
                'vehicle_id' => null, 
                'departure_time' => null,
                'arrival_time' => null,
                'check_in_time' => null,
                'check_out_time' => null,
            ],
            [
                'package_id' => 1, 
                'from_location_id' => 2, 
                'to_location_id' => 4, 
                'handled_by_courier_id' => null, 
                'vehicle_id' => null, 
                'departure_time' => null,
                'arrival_time' => null,
                'check_in_time' => null,
                'check_out_time' => null,
            ],
            [
                'package_id' => 1, 
                'from_location_id' => 4, 
                'to_location_id' => 7, 
                'handled_by_courier_id' => null, 
                'vehicle_id' => null,
                'departure_time' => null,
                'arrival_time' => null,
                'check_in_time' => null,
                'check_out_time' => null,
            ]

        ]);
    }
}
