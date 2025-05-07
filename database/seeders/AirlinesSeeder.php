<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AirlinesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('airlines')->insert([
            [
                'name' => 'Brussels Airlines',
                'IATA_code' => 'SN',
                'contact_number' => '+32 2 723 23 45',
                'headquarters_location' => 'Brussels',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'KLM',
                'IATA_code' => 'KL',
                'contact_number' => '+31 20 474 77 47',
                'headquarters_location' => 'Amsterdam',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Lufthansa',
                'IATA_code' => 'LH',
                'contact_number' => '+49 69 86 799 799',
                'headquarters_location' => 'Frankfurt',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Air France',
                'IATA_code' => 'AF',
                'contact_number' => '+33 1 41 56 78 00',
                'headquarters_location' => 'Paris',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Ryanair',
                'IATA_code' => 'FR',
                'contact_number' => '+353 1 945 12 12',
                'headquarters_location' => 'Dublin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);

        \App\Models\Airline::factory(50)->create();

    }
}
