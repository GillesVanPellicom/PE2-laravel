<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeliveryMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('delivery_method')->insert([
            [
                'code' => 'PICKUP_POINT',
                'name' => 'Pickup Point',
                'description' => 'Local pickup point',
                'price' => 5.99,
                'requires_location' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'Parcel Locker',
                'name' => 'Parcel Locker',
                'description' => 'Secure parcel locker',
                'price' => 7.99,
                'requires_location' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'address',
                'name' => 'Home Address',
                'description' => 'Deliver to your doorstep',
                'price' => 9.99,
                'requires_location' => false,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
};