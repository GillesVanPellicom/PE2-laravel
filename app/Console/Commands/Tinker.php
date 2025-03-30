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

/**
 * Class KdTree
 *
 * Represents a k-D tree. Used for nearest neighbor search in a k-D space.
 *
 * @package App\Services\Router\Types
 */
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
    /** @var Router $router */
    $router = App::make(Router::class);
//    $router->addRoute("@AIR_EFHK", "@AIR_LGAV", 8);
//    $router->removeRoute("@AIR_EFHK", "@AIR_LGAV", 8);

    $package = Package::find(1);

    $path = $package->getMovements();
    dd($path);
  }
}