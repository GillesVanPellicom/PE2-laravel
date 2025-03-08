<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
=======
use Carbon\Carbon;
>>>>>>> 273ca822b4ef9c588497bf89ad65b8e2f40bd0e9

class DeliveryMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('delivery_method')->insert([
<<<<<<< HEAD
            [
                'code' => 'Pickup Point',
                'name' => 'Pickup Point',
                'description' => 'Local pickup point',
                'price' => 5.99,
                'requires_location' => true,
                'is_active' => true
            ],
            [
                'code' => 'Parcel Locker',
                'name' => 'Parcel Locker',
                'description' => 'Secure parcel locker',
                'price' => 7.99,
                'requires_location' => true,
                'is_active' => true
            ],
            [
                'code' => 'address',
                'name' => 'Home Address',
                'description' => 'Deliver to your doorstep',
                'price' => 9.99,
                'requires_location' => false,
                'is_active' => true
            ]
        ]);
    }
}
=======
            'name' => "Standard",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
>>>>>>> 273ca822b4ef9c588497bf89ad65b8e2f40bd0e9
