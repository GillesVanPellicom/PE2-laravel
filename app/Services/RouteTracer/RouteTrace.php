<?php

namespace App\Services\RouteTracer;

use App\Services\Router\GeoMath;

class RouteTrace
{
    public function __construct() {
        
      }

    protected $maxDistance = 250; //MAX work distance in KM

    public function generateRoute(array $packages)
    {
        
        $route = [];
        $totalDistance = 0;

        if (empty($packages)) {
            return $route;
        }
        $currentLocation = $packages[0];
        $route[] = $currentLocation;
        
        while (count($route) < count($packages) && $totalDistance < $this->maxDistance) {
            $nextPackage = $this->findClosestPackage($currentLocation, $packages, $route);

            if (!$nextPackage) {
                break;
            }

            $distance = GeoMath::sphericalCosinesDistance(
                deg2rad($currentLocation['latitude']),
                deg2rad($currentLocation['longitude']),
                deg2rad($nextPackage['latitude']),
                deg2rad($nextPackage['longitude'])
            );

            if ($totalDistance + $distance > $this->maxDistance) {
                break; 
            }

            $route[] = $nextPackage;
            $totalDistance += $distance;

            $currentLocation = $nextPackage;
        }

        return $route;
    }
    private function findClosestPackage($currentLocation, $packages, $route)
    {
        $closestDistance = PHP_INT_MAX;
        $closestPackage = null;

        foreach ($packages as $package) {
            if (in_array($package, $route)) {
                continue; // Skip packages already in the route
            }

            $distance = GeoMath::sphericalCosinesDistance(
                deg2rad($currentLocation['latitude']),
                deg2rad($currentLocation['longitude']),
                deg2rad($package['latitude']),
                deg2rad($package['longitude'])
            );

            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closestPackage = $package;
            }
        }

        return $closestPackage;
    }
}
