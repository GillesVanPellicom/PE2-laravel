<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('packages')->insert([
            'reference' => 'REF125496',
            'customer_id' => 1,
            'origin_location_id' => 1,
            'current_location_id' => 2,
            'destination_location_id' => 3,
            'addresses_id' => 4,
            'status' => 'Pending',
            'weight_id' => 1,
            'delivery_method_id' => 1,
            'dimension' => '30x20x15',
            'name' => 'John',
            'lastName' => 'Doe',
            'receiverEmail' => 'john.doe@example.com',
            'receiver_phone_number' => '+32456789012',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
