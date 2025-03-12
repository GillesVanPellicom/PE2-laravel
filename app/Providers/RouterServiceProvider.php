<?php

namespace App\Providers;

use App\Services\Router\Router;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class RouterServiceProvider extends ServiceProvider
{
    /**
     * Register routing services.
     */
    public function register(): void
    {
      $this->app->singleton(Router::class, function (Application $app) {
        return new Router();
      });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
