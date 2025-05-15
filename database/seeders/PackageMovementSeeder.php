<?php

namespace Database\Seeders;

use App\Helpers\ConsoleHelper;
use App\Models\Package;
use App\Services\Router\Router;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Throwable;

class PackageMovementSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $packages = Package::all();
    $totalPackages = $packages->count();
    ConsoleHelper::info("Starting mass package routing...");

    /** @var Router $router */
    $router = App::make(Router::class);

    $debugAltered = false;
    if ($router->isDebugMode()) {
      ConsoleHelper::warn("Router debug mode is enabled. Disabling for mass routing, will re-enable at the end.");
      $router->setDebugMode(false);
      $debugAltered = true;
    }

    $startTime = microtime(true);
    $executionTimes = [];

    foreach ($packages as $package) {
      try {
        $packageStartTime = microtime(true);
        ConsoleHelper::task(str_pad("[$package->id]", 7, ' ', STR_PAD_RIGHT)."$package->reference",
          function () use ($package) {
            $package->generateMovements();
          });
        $executionTimes[] = (microtime(true) - $packageStartTime) * 1000; // Convert to milliseconds
      } catch (Throwable $e) {
        ConsoleHelper::error("Error routing package REF: $package->reference, ID: $package->id: ".$e->getMessage());
        ConsoleHelper::info("[Package Details] ".json_encode($package->toArray(), JSON_PRETTY_PRINT));
      }
    }

    $endTime = microtime(true);
    $totalTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
    $averageTime = $totalPackages > 0 ? $totalTime / $totalPackages : 0;

    // Calculate geometric mean safely
    if ($totalPackages > 0 && !empty($executionTimes)) {
      $logSum = array_sum(array_map('log', array_filter($executionTimes, fn($time) => $time > 0)));
      $geomean = exp($logSum / count($executionTimes));
    } else {
      $geomean = 0;
    }

    if ($debugAltered) {
      $router->setDebugMode(true);
    }

    ConsoleHelper::info("[Statistics] $totalPackages packages seeded. Time: ".round($totalTime,
        2)."ms (total), ".round($averageTime, 2)."ms (avg), ".round($geomean, 2)."ms (geomean)");
    ConsoleHelper::success("Routing completed.");

    $package = Package::find(2);
    while ($package->movements()->firstWhere("current_node_id", $package->current_location_id)->next_movement != null){
      $package->fakeMove();
    }

    $package = Package::find(3);
    while ($package->movements()->firstWhere("current_node_id", $package->current_location_id)->next_movement != null){
      $package->fakeMove();
    }

    $package = Package::find(4);
    while ($package->movements()->firstWhere("current_node_id", $package->current_location_id)->nextHop->next_movement != null){
      $package->fakeMove();
    }
  }
}