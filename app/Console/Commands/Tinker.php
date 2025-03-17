<?php

namespace App\Console\Commands;

use App\Models\Package;
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
    /** @var Package $package */
    $package = Package::find(1);
    $path = $package->getMovements();
    dd ($path[1]->getType());
  }
}
