<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CourierRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $employees = User::role('courier')->get()->pluck('employee')->all();
        foreach($employees as $employee){
            DB::table('courier_routes')->insert([
                'courier' => $employee->id,
                'start_location' => null,
                'current_location' => null,
                'end_location' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
