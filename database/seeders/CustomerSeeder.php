<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customers')->insert([
            'first_name' => 'Alice',
            'last_name' => 'Johnson',
            'email' => 'alice.johnson@example.com',
            'phone_number' => '+32456789012',
            'address_id' => 1, // Ensure this address exists in the 'addresses' table
            'registration_date' => Carbon::now()->toDateString(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
