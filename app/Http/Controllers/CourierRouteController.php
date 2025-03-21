<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\PackageMovement;
use App\Models\Location;
use App\Services\RouteTracer\RouteTrace;

class CourierRouteController extends Controller
{
    public function showRoute()
    {
        // Step 1: Get all packages with destination type PRIVATE_INDIVIDU
        $packages = Package::with(['currentLocation', 'destinationLocation', 'movements'])
            ->whereHas('destinationLocation', function ($query) {
                $query->where('location_type', 'PRIVATE_INDIVIDU');
            })
            ->get();
        
            dump($packages);


        // Debug: Check if packages are retrieved
        if ($packages->isEmpty()) {
            return view('courier.route', ['route' => []]);
        }

        // Step 2: Filter packages by last movement's current location being a DISTRIBUTION_CENTER
        $filteredPackages = $packages->filter(function ($package) {
            $lastMovement = $package->movements()->latest('created_at')->first();

            if (!$lastMovement) {
                return false; // Skip packages without movements
            }

            // Ensure the last movement's destination is PRIVATE_INDIVIDU and hopArrived is FALSE
            return $lastMovement->toLocation
                && $lastMovement->toLocation->location_type === 'PRIVATE_INDIVIDU'
                && (!$lastMovement->hopArrived || $lastMovement->hopArrived === 0);
        });

        // Debug: Check if filtered packages are retrieved
        if ($filteredPackages->isEmpty()) {
            return view('courier.route', ['route' => []]);
        }

        // Step 3: Prepare package data for route calculation
        $packageData = $filteredPackages->map(function ($package) {
            return [
                'latitude' => $package->destinationLocation->latitude,
                'longitude' => $package->destinationLocation->longitude,
                'ref' => $package->reference, // Include the reference number
            ];
        })->toArray();

        // Debug: Check if package data is prepared
        if (empty($packageData)) {
            return view('courier.route', ['route' => []]);
        }

        // Step 4: Calculate the route using RouteTrace
        $route = [];
        if (!empty($packageData)) {
            $routeCreator = new RouteTrace();
            $route = $routeCreator->generateRoute($packageData);
        }


        // Step 5: Pass the route to the Blade view
        // return view('courier.route', ['route' => $route ?? []]);
    
    }
}