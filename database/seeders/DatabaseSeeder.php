<?php

namespace Database\Seeders;

use App\Models\User;

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
      CountriesSeeder::class,       // 🔥 Countries after cities
      CitiesSeeder::class,          // 🔥 Cities after locations
      AddressesSeeder::class,
      LocationsSeeder::class,       // 🔥 First location because it's a foreign key
      EmployeesSeeder::class,
      FunctionsSeeder::class,
      TeamsSeeder::class,
      PayrollSeeder::class,
      AirlinesSeeder::class,
      AirplanesSeeder::class,
      AirportsSeeder::class,
      ContractsSeeder::class,
      VehiclesSeeder::class,
      DeliveryMethodSeeder::class,
      WeightClassSeeder::class,
      CustomerSeeder::class,
      PackageSeeder::class,
      PackageMovementsSeeder::class,
      FlightsSeeder::class,
    ]);

  }
}
