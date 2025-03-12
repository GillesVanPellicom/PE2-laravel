<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('employees')->insert([
            ['first_name' => 'Jordi', 'last_name' => 'Schoetens', 'email' => 'r0983966@student.thomasmore.be', 'phone_number' => '123456789', 'birth_date' => '2005-02-05', 'address_id' => 8, 'nationality' => 'Belgian', 'leave_balance' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['first_name' => 'Szymon', 'last_name' => 'Bartus Piotr', 'email' => 'szymon@example.com', 'phone_number' => '987654321', 'birth_date' => '2005-02-05', 'address_id' => 7, 'nationality' => 'Polish', 'leave_balance' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['first_name' => 'Thomas', 'last_name' => 'De Clerck', 'email' => 'thomas@example.com', 'phone_number' => '456123789', 'birth_date' => '2005-02-05', 'address_id' => 6, 'nationality' => 'Belgian', 'leave_balance' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['first_name' => 'Julien', 'last_name' => 'Stuckens', 'email' => 'julien@example.com', 'phone_number' => '321456987', 'birth_date' => '2005-02-05', 'address_id' => 5, 'nationality' => 'Belgian', 'leave_balance' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['first_name' => 'Keith', 'last_name' => 'Cnops', 'email' => 'keith@example.com', 'phone_number' => '789321654', 'birth_date' => '2005-02-05', 'address_id' => 4, 'nationality' => 'Belgian', 'leave_balance' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['first_name' => 'Fenno', 'last_name' => 'Feremans', 'email' => 'fenno@example.com', 'phone_number' => '456789123', 'birth_date' => '2005-02-05', 'address_id' => 3, 'nationality' => 'Belgian', 'leave_balance' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['first_name' => 'Senne', 'last_name' => 'De Vrinedt', 'email' => 'senne@example.com', 'phone_number' => '123987654', 'birth_date' => '2005-02-05', 'address_id' => 2, 'nationality' => 'Belgian', 'leave_balance' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['first_name' => 'Siebe', 'last_name' => 'Van Hirtum', 'email' => 'siebe@example.com', 'phone_number' => '654321987', 'birth_date' => '2005-02-05', 'address_id' => 1, 'nationality' => 'Belgian', 'leave_balance' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['first_name' => 'Omrane', 'last_name' => 'unknown', 'email' => 'omrane@example.com', 'phone_number' => '741258963', 'birth_date' => '2005-02-05', 'address_id' => 8, 'nationality' => 'Belgian', 'leave_balance' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['first_name' => 'Gilles', 'last_name' => 'Van Pellicom', 'email' => 'gilles@example.com', 'phone_number' => '369852147', 'birth_date' => '2005-02-05', 'address_id' => 8, 'nationality' => 'Belgian', 'leave_balance' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['first_name' => 'Kudsi', 'last_name' => 'unknown', 'email' => 'kudsi@example.com', 'phone_number' => '852147963', 'birth_date' => '2005-02-05', 'address_id' => 8, 'nationality' => 'Belgian', 'leave_balance' => 25, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
