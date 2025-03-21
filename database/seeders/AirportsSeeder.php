<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AirportsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('airports')->insert([
            ['location_id' => 1, 'name' => 'BRU', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Brussels Airport
            ['location_id' => 1, 'name' => 'AMS', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Liège Airport
            ['location_id' => 1, 'name' => 'BER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Antwerp International Airport
        ]);
    }
}
