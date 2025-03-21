<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void // REMOVE THIS AND I WILL FIND U, AT LEAST GIVE ME 1 ROUTE TO WORK WITH GOOFY
    {
        DB::table('package_movements')->insert([
            [
                'package_id' => 1, 
                'from_location_id' => 1, 
                'to_location_id' => 9, 
                'handled_by_courier_id' => null, 
                'vehicle_id' => null, 
                'departure_time' => null,
                'arrival_time' => null,
                'check_in_time' => null,
                'check_out_time' => null,
                'node_id' => 1,
                'next_hop' => null,
                'router_edge_id' => null,
                'hopDeparted' => false,
                'hopArrived' => false,
            ]
            ]);
    }
}
