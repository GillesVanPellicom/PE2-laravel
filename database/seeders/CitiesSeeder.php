<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cities')->insert([
            [
                'name' => 'Antwerp',
                'postcode' => '2000',
                'country_id' => 17, // Belgium
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Brussels',
                'postcode' => '1000',
                'country_id' => 17, // Belgium
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Amsterdam',
                'postcode' => '1011',
                'country_id' => 126, // Netherlands
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rotterdam',
                'postcode' => '3011',
                'country_id' => 126, // Netherlands
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'London',
                'postcode' => 'EC1A',
                'country_id' => 186, // United Kingdom
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manchester',
                'postcode' => 'M1',
                'country_id' => 186, // United Kingdom
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Madrid',
                'postcode' => '28001',
                'country_id' => 164, // Spain
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Barcelona',
                'postcode' => '08001',
                'country_id' => 164, // Spain
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Berlin',
                'postcode' => '10115',
                'country_id' => 64, // Germany
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paris',
                'postcode' => '75001',
                'country_id' => 60, // France
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
?>