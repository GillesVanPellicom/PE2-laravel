<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('countries')->insert([
            [
                'country_name' => 'Belgium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_name' => 'Netherlands',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_name' => 'Germany',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
?>