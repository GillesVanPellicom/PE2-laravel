<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleHelper;
use App\Models\Package;
use App\Services\Router\Router;
use App\Services\Router\Types\Node;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class Tinker extends Command {

protected $signature = 'gilles:tinker';

protected $description = "Gilles' tinker workspace";



public function handle(): void {
    for ($i = 1; $i <= 101; $i++) {
      $package = Package::find($i);
      try {
        $path = $package->getMovements();
        for($j = 0; $j <= 16; $j++) {
          $package->fakeMove();
        }
      } catch (Exception $e) {
        ConsoleHelper::error($e->getMessage());
      }
    }
}
}