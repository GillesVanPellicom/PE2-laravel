<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Location;

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
        'description' => 'Brussels Private Individu 0',
        'location_type' => 'ADDRESS',
        'latitude' => 50.89927,
        'longitude' => 4.41279,
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
        'latitude' => 50.84927,
        'longitude' => 4.41279,
        'addresses_id' => 1, // placeholder
        'contact_number' => '+32 2 123 45 67', // placeholder
        'opening_hours' => null, // placeholder
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'Einhoven Private Individu 1',
        'location_type' => 'ADDRESS',
        'latitude' => 51.4469,
        'longitude' => 5.4034,
        'addresses_id' => 1, // placeholder
        'contact_number' => '+32 2 123 45 67', // placeholder
        'opening_hours' => null, // placeholder
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'Madrid Private Individu 1',
        'location_type' => 'ADDRESS',
        'latitude' => 40.4644,
        'longitude' => -3.5882,
        'addresses_id' => 1, // placeholder
        'contact_number' => '+32 2 123 45 67', // placeholder
        'opening_hours' => null, // placeholder
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'Rome Private Individu 1',
        'location_type' => 'ADDRESS',
        'latitude' => 41.8606,
        'longitude' => 12.4543,
        'addresses_id' => 1, // placeholder
        'contact_number' => '+32 2 123 45 67', // placeholder
        'opening_hours' => null, // placeholder
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'Heraklion Private Individu 1',
        'location_type' => 'ADDRESS',
        'latitude' => 35.31960,
        'longitude' => 25.1378,
        'addresses_id' => 1, // placeholder
        'contact_number' => '+32 2 123 45 67', // placeholder
        'opening_hours' => null, // placeholder
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'HR OFFICE 1',
        'location_type' => 'OFFICE',
        'latitude' => 50.8413,
        'longitude' => 4.3683,
        'addresses_id' => 10,
        'contact_number' => '+32 2 123 55 88',
        'opening_hours' => null,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'HR OFFICE 2',
        'location_type' => 'OFFICE',
        'latitude' => 40.4508,
        'longitude' => -3.6917,
        'addresses_id' => 11, // placeholder
        'contact_number' => '+32 2 156 75 17', // placeholder
        'opening_hours' => null, // placeholder
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'HR OFFICE 3',
        'location_type' => 'OFFICE',
        'latitude' => 34.1758,
        'longitude' => -118.5968,
        'addresses_id' => 12, // placeholder
        'contact_number' => '+32 2 956 12 54', // placeholder
        'opening_hours' => null, // placeholder
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
    Location::factory()->count(100)->create();
  }
}
