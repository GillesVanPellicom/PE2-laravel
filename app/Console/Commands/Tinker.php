<?php

namespace App\Console\Commands;

use App\Services\Router\Router;
use App\Services\Router\Types\Node;
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
  protected $description = 'My tinker command';

  /**
   * Execute the console command.
   */
  public function handle(): void {
    /** @var Router $router */
    $router = App::make(Router::class);
    $router->setDebug(false);

//    $path = $router->generate(
//      '7019 Forbes Ave, Lake Balboa, CA 91406, USA',
//      'Kommerzienrat-Meindl-Straße 1, 84405 Dorfen, Germany');
    $path = $router->generate(
      '7019 Forbes Ave, Lake Balboa, CA 91406, USA',
      '@PIP_0001');

    Router::printPath($path);

//    echo $path[0]->getDescription();
  }
}
