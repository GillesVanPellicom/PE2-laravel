<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RouteTracer\RouteTrace;

class TestRouteGeneration extends Command
{
    protected $signature = 'test:route';  // The name of the Artisan command
    protected $description = 'Test route generation logic';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Example package data
        $packages = [
            ['latitude' => 51.2194, 'longitude' => 4.4025],
            ['latitude' => 51.0543, 'longitude' => 3.7174],
            ['latitude' => 50.8503, 'longitude' => 4.3517],
            ['latitude' => 50.9375, 'longitude' => 6.9603],
            ['latitude' => 51.7443, 'longitude' => 3.1274],
        ];

        $routeCreator = new RouteTrace();  // Instantiate the RouteTrace class
        $route = $routeCreator->generateRoute($packages);  // Generate the route

        // Output the result to the console
        $this->info('Generated Route:');
        $this->info(json_encode($route, JSON_PRETTY_PRINT));  // Display the route
    }
}
