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
      RouterNodesSeeder::class,
      RouterEdgesSeeder::class,
      PackageSeeder::class,
      InvoiceSeeder::class,
      VacationSeeder::class,
      FlightsSeeder::class,
      FlightsContractSeeder::class,
      UserRoleSeeder::class,
      MessageTemplatesSeeder::class,
      VehiclesSeeder::class,
      CourierSeeder::class,
      TicketSeeder::class,
      PackageMovementSeeder::class,
    ]);

  }
}
