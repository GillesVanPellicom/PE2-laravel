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
                'country_id' => 1, // Belgium
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Brussels',
                'postcode' => '1000',
                'country_id' => 1, // Belgium
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Amsterdam',
                'postcode' => '1011',
                'country_id' => 2, // Netherlands
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Berlin',
                'postcode' => '10115',
                'country_id' => 3, // Germany
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
?>