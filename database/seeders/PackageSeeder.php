<?php

namespace Database\Seeders;

use App\Helpers\ConsoleHelper;
use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
//    DB::table('packages')->insert([
//      'reference' => 'REF125496',
//      'user_id' => 1,
//      'origin_location_id' => '@DOP_0001',
//      'destination_location_id' => '6',
//      'addresses_id' => 4,
//      'status' => 'Pending',
//      'weight_id' => 1,
//      'delivery_method_id' => 1,
//      'dimension' => '30x20x15',
//      'name' => 'John',
//      'lastName' => 'Doe',
//      'receiverEmail' => 'john.doe@example.com',
//      'receiver_phone_number' => '+32456789012',
//      'created_at' => Carbon::now(),
//      'updated_at' => Carbon::now(),
//    ]);
    DB::table('packages')->insert([
      'reference' => 'REF125496',
      'user_id' => 1,
      'origin_location_id' => '6',
      'destination_location_id' => '@PIP_0001',
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
    Package::factory()->count(100)->create();
  }
}
