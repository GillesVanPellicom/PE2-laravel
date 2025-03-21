<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsSeeder extends Seeder {
  public function run(): void {

    // NOTICE
    // Make sure any latitude and longitude values are realistic.
    // Actually check them on a map.
    // The router does not work if you use fictional values.

    DB::table('locations')->insert([
      [
        'description' => 'tmpdesc: 7019 Forbes Ave, Lake Balboa, CA 91406, USA',
        'location_type' => 'ADDRESS',
        'infrastructure_id' => null,
        'latitude' => 34.19831,
        'longitude' => -118.49897,
        'addresses_id' => 1, // placeholder
        'contact_number' => '+32 2 123 45 67', // placeholder
        'opening_hours' => null, // placeholder
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'Antwerp Pickup Point 1',
        'location_type' => 'PICKUP_POINT',
        'infrastructure_id' => '@PIP_0001',
        'latitude' => 51.18944,
        'longitude' => 4.46027,
        'addresses_id' => 1, // placeholder
        'contact_number' => '+32 2 123 45 67', // placeholder
        'opening_hours' => null, // placeholder
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'Brussels Private Individu 1',
        'location_type' => 'ADDRESS',
        'infrastructure_id' => null,
        'latitude' => 50.84927,
        'longitude' => 4.41279,
        'addresses_id' => 1, // placeholder
        'contact_number' => '+32 2 123 45 67', // placeholder
        'opening_hours' => null, // placeholder
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
