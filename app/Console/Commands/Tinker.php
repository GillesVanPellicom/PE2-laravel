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
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'gilles:tinker';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Gilles\' tinker workspace';

  /**
   * Execute the console command.
   */
  public function handle(): void {
    /** @var Package $package */
    for ($i = 1; $i <= 101; $i++) {
      $package = Package::find($i);
      try {
        $path = $package->getMovements();
        for($j = 0; $j <= 16; $j++) {
          $package->move();
        }
      } catch (Exception $e) {
        ConsoleHelper::error($e->getMessage());
      }
    }
}
}
