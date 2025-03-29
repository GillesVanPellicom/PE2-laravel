<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleHelper;
use App\Models\Location;
use App\Models\Package;
use App\Services\Router\Router;
use App\Services\Router\Types\Exceptions\InvalidCoordinateException;
use App\Services\Router\Types\Exceptions\InvalidRouterArgumentException;
use App\Services\Router\Types\Exceptions\NodeNotFoundException;
use App\Services\Router\Types\Exceptions\NoPathFoundException;
use App\Services\Router\Types\Exceptions\RouterException;
use App\Services\Router\Types\Node;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class Tinker extends Command {

  protected $signature = 'gilles:tinker';

  protected $description = "Gilles' tinker workspace";


  /**
   * @throws RouterException
   * @throws InvalidRouterArgumentException
   * @throws NodeNotFoundException
   * @throws InvalidCoordinateException
   * @throws NoPathFoundException
   */
  public function handle(): void {
    $package = Package::find(1);

    $package->return();

    $path = $package->getMovements();
//      dd($path);
  }
}