<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FlightsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('flights')->insert([
            ['airplane_id' => 1,'depart_location_id'=>1,'arrive_location_id'=>1,'departure_time'=>Carbon::now(),'arrival_time'=>Carbon::now(),'status'=>'flying','created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ]);
    }
}
