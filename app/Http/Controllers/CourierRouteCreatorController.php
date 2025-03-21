<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Services\RouteHelper;

class RouteCreatorController extends Controller
{
    public function createRoute()
    {

        $routeHelper = new RouteHelper();

        $maxDistance = 250; // Maximum distance in kilometers
        $totalDistance = 0;
        $route = [];
        
        // Get all packages with 'PRIVATE_INDIVIDU' as destination
        $packages = Package::with('destinationLocation') // Assuming destination_location_id is related to Location model
                            ->whereHas('destinationLocation', function ($query) {
                                $query->where('location_type', 'PRIVATE_INDIVIDU');
                            })
                            ->get();

        // Assuming we start with the first package in the list (this could be dynamic)
        $currentPackage = $packages->first();
        $route[] = $currentPackage;
        
        // Remove the selected package from the list
        $packages = $packages->filter(function($package) use ($currentPackage) {
            return $package->id !== $currentPackage->id;
        });
        
        // Start creating the route
        while ($totalDistance < $maxDistance && $packages->isNotEmpty()) {
            $shortestDistance = null;
            $nextPackage = null;
            
            // Calculate the distance to each remaining package
            foreach ($packages as $package) {
                $distance = RouteHelper::haversineDistance(
                    $currentPackage->destinationLocation->latitude,
                    $currentPackage->destinationLocation->longitude,
                    $package->destinationLocation->latitude,
                    $package->destinationLocation->longitude
                );
                
                // Find the nearest package
                if (is_null($shortestDistance) || $distance < $shortestDistance) {
                    $shortestDistance = $distance;
                    $nextPackage = $package;
                }
            }

            // Add the nearest package to the route
            $route[] = $nextPackage;
            $totalDistance += $shortestDistance;
            
            // Remove the selected package from the list
            $packages = $packages->filter(function($package) use ($nextPackage) {
                return $package->id !== $nextPackage->id;
            });

            // Update the current package for the next iteration
            $currentPackage = $nextPackage;
        }

        // Return the calculated route
        return response()->json([
            'route' => $route,
            'total_distance' => $totalDistance
        ]);
    }
}
