<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AddressesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('addresses')->insert([
            // Airports
            [
                'street' => 'Brussels Airport Street',
                'house_number' => '1',
                'cities_id' => 2, // Brussels
                'country_id' => 1, // Belgium
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'street' => 'Schiphol Boulevard',
                'house_number' => '101',
                'cities_id' => 3, // Amsterdam
                'country_id' => 2, // Netherlands
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'street' => 'Willy-Brandt-Platz',
                'house_number' => '1',
                'cities_id' => 4, // Berlin
                'country_id' => 3, // Germany
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Distribution Centers
            [
                'street' => 'Brussels DC Street',
                'house_number' => '50',
                'cities_id' => 1, // Brussels
                'country_id' => 1, // Belgium
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'street' => 'Amsterdam DC Street',
                'house_number' => '75',
                'cities_id' => 2, // Amsterdam
                'country_id' => 2, // Netherlands
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Pickup Points
            [
                'street' => 'Antwerp Pickup Street',
                'house_number' => '12',
                'cities_id' => 4, // Antwerp
                'country_id' => 1, // Belgium
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'street' => 'Berlin Pickup Street',
                'house_number' => '25',
                'cities_id' => 3, // Berlin
                'country_id' => 3, // Germany
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'street' => 'Jan Pieter De Nayerlaan',
                'house_number' => '5',
                'cities_id' => 2, // Brussels
                'country_id' => 1, // Belgium
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
?>