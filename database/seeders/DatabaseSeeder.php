<?php

namespace Database\Seeders;

use App\Models;
use App\Models\EmployeeFunction;
use App\Models\EmployeeContract;
use App\Http\Controllers\EmployeeController;
use App\Models\Airline;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   */
  public function run(): void {

    // Seed roles and permissions
    $this->call([
      RolesAndPermissionsSeeder::class,
      CountriesSeeder::class,       
      CitiesSeeder::class,          
      AddressesSeeder::class,
      LocationsSeeder::class,
      UserSeeder::class,
      TeamsSeeder::class,       
      EmployeesSeeder::class,
      FunctionsSeeder::class,
      PayrollSeeder::class,
      AirlinesSeeder::class,
      AirplanesSeeder::class,
      AirportsSeeder::class,
      ContractsSeeder::class,
      DeliveryMethodSeeder::class,
      WeightClassSeeder::class,
      PackageSeeder::class,
      FlightsSeeder::class,
      VacationSeeder::class,
      FlightsContractSeeder::class,
      RouterNodesSeeder::class,
      RouterEdgesSeeder::class,
      UserRoleSeeder::class,
      MessageTemplatesSeeder::class,
      VehiclesSeeder::class,
      CourierSeeder::class,
      TicketSeeder::class,
    ]);

    \App\Models\Airline::factory(50)->create();
    \App\Models\Airplane::factory(50)->create();


    /*\App\Models\User::factory(1000)->create();
    \App\Models\Team::factory(50)->create();
    \Database\Factories\FunctionFactory::new()->count(50)->create();
    
    // Ensure unique user_id values for employees with user_id > 50
    $userIds = \App\Models\User::where('id', '>', 50)->doesntHave('employee')->pluck('id')->shuffle()->take(200);
    foreach ($userIds as $userId) {
        \App\Models\Employee::factory()->create(['user_id' => $userId]);
    }

    // Ensure one employee cannot have two contracts at the same time
    $contractUserIds = \App\Models\Employee::doesntHave('contracts')->pluck('id')->shuffle()->take(200);
    foreach ($contractUserIds as $employeeId) {
        \Database\Factories\ContractFactory::new()->create(['employee_id' => $employeeId]);
    }

    $contracts = EmployeeContract::all();
    foreach($contracts as $contract) {
        EmployeeController::generateEmployeeContract($contract->contract_id);
    }

    \App\Models\Vacation::factory(4000)->create();
    \App\Models\Package::factory()->count(300)->create();*/

  }
}
