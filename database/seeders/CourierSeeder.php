<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\EmployeeFunction;
use App\Models\Address;
use App\Models\City;
use App\Models\Team;
use App\Models\Location;
use App\Models\RouterNodes;
use Spatie\Permission\Models\Role;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        // Antwerp city
        $antwerpCity = City::where('name', 'Antwerp')->first();
        if (!$antwerpCity) {
            throw new \Exception('Antwerp city not found');
        }
        // Logistics team
        $logisticsTeam = Team::where('department', 'Logistics')->first();

        // Distribution center location
        $distributionCenter = RouterNodes::where('id', '@DC_ANTWERP')->first();
        // Gebruik een willekeurige bestaande location
        $dcLocation = Location::where('location_type', 'OFFICE')
        ->orWhere('location_type', 'DISTRIBUTION_CENTER')
        ->first();

        if (!$dcLocation) {
        throw new \Exception('No suitable location found for contracts');
        }

        \Log::info('Using alternative location:', [
        'location_id' => $dcLocation->id,
        'description' => $dcLocation->description,
        'type' => $dcLocation->location_type
        ]);

        \Log::info('Distribution Center Info:', [
            'dc_id' => $distributionCenter->id,
            'location_id' => $distributionCenter->location_id,
            'location' => $dcLocation
        ]);

        // Create courier role and function
        $courierRole = Role::firstOrCreate(['name' => 'courier']);
        $courierFunction = EmployeeFunction::firstOrCreate(
            ['name' => 'courier'],
            [
                'role' => 'courier',
                'description' => 'Package delivery courier',
                'salary_min' => 2500,
                'salary_max' => 3500
            ]
        );

        // Courier data
        $couriers = [
            [
                'first_name' => 'John',
                'last_name' => 'Delivery',
                'email' => 'john.delivery@courier.com',
                'phone_number' => '+32471234567',
                'birth_date' => '1990-01-01',
                'address' => [
                    'street' => 'Courier Street',
                    'house_number' => '101',
                    'cities_id' => $antwerpCity->id
                ]
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Express',
                'email' => 'sarah.express@courier.com',
                'phone_number' => '+32472345678',
                'birth_date' => '1992-05-15',
                'address' => [
                    'street' => 'Courier Street',
                    'house_number' => '102',
                    'cities_id' => $antwerpCity->id
                ]
            ],
            [
                'first_name' => 'Mike',
                'last_name' => 'Swift',
                'email' => 'mike.swift@courier.com',
                'phone_number' => '+32473456789',
                'birth_date' => '1988-12-20',
                'address' => [
                    'street' => 'Courier Street',
                    'house_number' => '103',
                    'cities_id' => $antwerpCity->id
                ]
            ]
        ];

        // Create couriers
        foreach ($couriers as $courierData) {
            // Skip if exists
            if (User::where('email', $courierData['email'])->exists()) {
                continue;
            }
        
            // Create address
            $address = Address::create($courierData['address']);
        
            // Create user
            $user = User::create([
                'first_name' => $courierData['first_name'],
                'last_name' => $courierData['last_name'],
                'email' => $courierData['email'],
                'phone_number' => $courierData['phone_number'],
                'birth_date' => $courierData['birth_date'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'address_id' => $address->id
            ]);
        
            // Assign role
            $user->assignRole($courierRole);
        
            // Create employee
            $employee = Employee::create([
                'user_id' => $user->id,
                'team_id' => $logisticsTeam->id,
                'leave_balance' => 20
            ]);
        
            // Create contract
            EmployeeContract::create([
                'employee_id' => $employee->id,
                'job_id' => $courierFunction->id,
                'location_id' => $dcLocation->id,
                'start_date' => now()
            ]);
        }
    }
}