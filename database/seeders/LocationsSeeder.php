<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsSeeder extends Seeder {
  public function run(): void {
    DB::table('locations')->insert([
      [
        'description' => 'Brussels Private Individu 1',
        'location_type' => 'PRIVATE_INDIVIDU',
        'longitude' => 4.48444,
        'latitude' => 50.90138,
        'addresses_id' => 9, // Address ID for Parcel Locker
        'contact_number' => '+32 2 123 45 67',
        'opening_hours' => null,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'Brussels Private Individu 2',
        'location_type' => 'PRIVATE_INDIVIDU',
        'longitude' => 4.48444,
        'latitude' => 50.90138,
        'addresses_id' => 9, // Address ID for Parcel Locker
        'contact_number' => '+32 2 123 45 67',
        'opening_hours' => null,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'Brussels Private Individu 3',
        'location_type' => 'PRIVATE_INDIVIDU',
        'longitude' => 4.48444,
        'latitude' => 50.90138,
        'addresses_id' => 9, // Address ID for Parcel Locker
        'contact_number' => '+32 2 123 45 67',
        'opening_hours' => null,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'Brussels Private Individu 4',
        'location_type' => 'PRIVATE_INDIVIDU',
        'longitude' => 4.48444,
        'latitude' => 50.90138,
        'addresses_id' => 9, // Address ID for Parcel Locker
        'contact_number' => '+32 2 123 45 67',
        'opening_hours' => null,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'description' => 'Brussels Private Individu 5',
        'location_type' => 'PRIVATE_INDIVIDU',
        'longitude' => 4.48444,
        'latitude' => 50.90138,
        'addresses_id' => 9, // Address ID for Parcel Locker
        'contact_number' => '+32 2 123 45 67',
        'opening_hours' => null,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],

    ]);
  }
}

?>