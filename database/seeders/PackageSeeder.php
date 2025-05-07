<?php

namespace Database\Seeders;

use App\Helpers\ConsoleHelper;
use App\Models\Package;
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
            'user_id' => 1,
            'origin_location_id' => 6,
            'current_location_id' => 1,
            'destination_location_id' => 7,
            'addresses_id' => 4,
            'status' => 'Pending',
            'weight_id' => 1,
            'delivery_method_id' => 1,
            'dimension' => '30x20x15',
            'weight' => 50, // Adjusted weight for testing
            'name' => 'John',
            'lastName' => 'Doe',
            'receiverEmail' => 'john.doe@example.com',
            'receiver_phone_number' => '+32456789012',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('packages')->insert([
            'reference' => 'REF125495',
            'user_id' => 1,
            'origin_location_id' => 6,
            'current_location_id' => 1,
            'destination_location_id' => 5,
            'addresses_id' => 4,
            'status' => 'Pending',
            'weight_id' => 1,
            'delivery_method_id' => 1,
            'dimension' => '30x20x15',
            'weight' => 30, // Adjusted weight for testing
            'name' => 'John',
            'lastName' => 'Doe',
            'receiverEmail' => 'john.doe@example.com',
            'receiver_phone_number' => '+32456789012',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('packages')->insert([
            'reference' => 'REF125486',
            'user_id' => 1,
            'origin_location_id' => 6,
            'current_location_id' => 1,
            'destination_location_id' => 3,
            'addresses_id' => 4,
            'status' => 'Pending',
            'weight_id' => 1,
            'delivery_method_id' => 1,
            'dimension' => '30x20x15',
            'weight' => 20,
            'name' => 'John',
            'lastName' => 'Doe',
            'receiverEmail' => 'john.doe@example.com',
            'receiver_phone_number' => '+32456789012',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('packages')->insert([
            'reference' => 'REF195496',
            'user_id' => 1,
            'origin_location_id' => 6,
            'current_location_id' => 1,
            'destination_location_id' => 7,
            'addresses_id' => 4,
            'status' => 'Pending',
            'weight_id' => 1,
            'delivery_method_id' => 1,
            'dimension' => '30x20x15',
            'weight' => 70,
            'name' => 'John',
            'lastName' => 'Doe',
            'receiverEmail' => 'john.doe@example.com',
            'receiver_phone_number' => '+32456789012',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        Package::factory()->count(100)->create();
    }
}
