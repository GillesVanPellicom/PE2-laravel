<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('locations')->insert([
            // Airports
            [
                'name' => 'Brussels Airport',
                'location_type' => 'Airport',
                'addresses_id' => 1, // Address ID for Brussels Airport
                'contact_number' => '+32 2 753 77 53',
                'opening_hours' => '24/7',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Amsterdam Schiphol',
                'location_type' => 'Airport',
                'addresses_id' => 2, // Address ID for Schiphol
                'contact_number' => '+31 20 794 0800',
                'opening_hours' => '24/7',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Berlin Brandenburg Airport',
                'location_type' => 'Airport',
                'addresses_id' => 3, // Address ID for Berlin Brandenburg
                'contact_number' => '+49 30 609160910',
                'opening_hours' => '24/7',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Distribution Centers
            [
                'name' => 'Brussels Distribution Center',
                'location_type' => 'Distribution Center',
                'addresses_id' => 4, // Address ID for Distribution Center
                'contact_number' => '+32 2 123 45 67',
                'opening_hours' => '08:00 - 18:00',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Amsterdam Distribution Center',
                'location_type' => 'Distribution Center',
                'addresses_id' => 5, // Address ID for Distribution Center
                'contact_number' => '+31 20 345 67 89',
                'opening_hours' => '08:00 - 18:00',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Pickup Points
            [
                'name' => 'Antwerp Pickup Point',
                'location_type' => 'Pickup Point',
                'addresses_id' => 6, // Address ID for Pickup Point
                'contact_number' => '+32 3 456 78 90',
                'opening_hours' => '09:00 - 17:00',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Berlin Pickup Point',
                'location_type' => 'Pickup Point',
                'addresses_id' => 7, // Address ID for Pickup Point
                'contact_number' => '+49 30 123 456 789',
                'opening_hours' => '09:00 - 17:00',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            //Parcel Lockers
            [
                'name' => 'Antwerp Parcel Locker',
                'location_type' => 'Parcel Locker',
                'addresses_id' => 6, // Address ID for Parcel Locker
                'contact_number' => '+32 2 123 45 67',
                'opening_hours' => '08:00 - 18:00',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            //Private Individu test
            [
                'name' => 'Customer Home Test',
                'location_type' => 'Private Individu',
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