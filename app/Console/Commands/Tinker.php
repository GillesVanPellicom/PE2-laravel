<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleHelper;
use App\Models\Location;
use App\Models\Package;
use App\Services\Router\Router;
use App\Services\Router\Types\MoveOperationType;
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
    $package = Package::find(2);
    $path = $package->getMovements();
    dd($package->getNextMovement());

  }
}
