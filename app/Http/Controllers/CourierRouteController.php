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
        // Step 1: Retrieve all packages with destination type PRIVATE_INDIVIDU
        $packages = Package::with(['currentLocation', 'destinationLocation'])
            ->whereHas('destinationLocation', function ($query) {
                $query->where('location_type', 'PRIVATE_INDIVIDU');
            })
            ->get();

        // Step 2: Filter packages by their last movement's current location being a DISTRIBUTION_CENTER
        $filteredPackages = $packages->filter(function ($package) {
            $lastMovement = PackageMovement::where('package_id', $package->id)
                ->latest('created_at')
                ->first();

            if (!$lastMovement) {
                return false; // Skip packages with no movements
            }

            // Ensure the last movement's destination is PRIVATE_INDIVIDU and hopArrived is FALSE
            return $lastMovement->toLocation
                && $lastMovement->toLocation->location_type === 'PRIVATE_INDIVIDU'
                && !$lastMovement->hopArrived;
        });

        // Step 3: Check if there are any filtered packages
        if ($filteredPackages->isEmpty()) {
            dd('Filtered Packages:', $filteredPackages->toArray());
        }

        // Step 4: Prepare package data for route calculation
        $packageData = $filteredPackages->map(function ($package) {
            return [
                'latitude' => $package->currentLocation->latitude,
                'longitude' => $package->currentLocation->longitude,
            ];
        })->toArray();

        // Step 5: Calculate the route using RouteTrace
        $routeCreator = new RouteTrace();
        $route = $routeCreator->generateRoute($packageData);

        // Step 6: Pass the route to the Blade view
        return view('courier.route', ['route' => $route]);
    }
}