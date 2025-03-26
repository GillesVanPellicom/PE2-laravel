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
            [
                'name' => 'Los Angeles',
                'postcode' => '90001',
                'country_id' => 187, // United States
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Munich',
                'postcode' => '80331',
                'country_id' => 64, // Germany
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Warsaw',
                'postcode' => '00-001',
                'country_id' => 140, // Poland
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Charleroi',
                'postcode' => '6000',
                'country_id' => 17, // Belgium
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tilburg',
                'postcode' => '5056',
                'country_id' => '126',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bastogne',
                'postocde' => '6600',
                'country_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ghent',
                'postocde' =>  '9000',
                'country_id' => 17,
                'created_at' => now(),
                'updated_at' => now(), 
            ],
            [
                'name' => 'Lille',
                'postocde' => '59000',
                'country_id' => 60,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Eindhoven',
                'postocde' => '5502',
                'country_id' => 126,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Malaga',
                'postcode' => '29001',
                'country_id' => 164, // Spain
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dusseldorf',
                'postcode' => '40474',
                'country_id' => 64, // Spain
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Katowice',
                'postcode' => '42-625',
                'country_id' => 140, // Poland
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Poznań',
                'postcode' => '60-189',
                'country_id' => 140, // Poland
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lisboa',
                'postcode' => '1700-111',
                'country_id' => 141, // Portugal
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Porto',
                'postcode' => '4000-008',
                'country_id' => 141, // Portugal
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rome',
                'postcode' => '00054',
                'country_id' => 82, // Italïe
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Milan',
                'postcode' => '20054',
                'country_id' => 82, // Italïe
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Athens',
                'postcode' => '19019',
                'country_id' => 66, // Griekenland
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Heraklion',
                'postcode' => '141 22',
                'country_id' => 66, // Griekenland
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
?>