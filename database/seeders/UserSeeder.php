<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone_number' => '1234567890',
                'birth_date' => '1990-01-01',
                'email' => 'john.doe@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'), 
                'address_id' => 1, 
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'phone_number' => '0987654321',
                'birth_date' => '1990-01-01',
                'email' => 'jane.doe@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'address_id' => 2,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'phone_number' => '1234567890',
                'birth_date' => '1990-01-01',
                'email' => 'john.smith@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'address_id' => 3,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'phone_number' => '0987654321',
                'birth_date' => '1990-01-01',
                'email' => 'jane.smith@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'address_id' => 4,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Courier',
                'phone_number' => '0987654321',
                'birth_date' => '1990-01-01',
                'email' => 'courier@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('courier'),
                'address_id' => 4,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
