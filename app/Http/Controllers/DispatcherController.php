<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RouterNodes;
use App\Models\City;
use App\Models\Package;
use App\Services\Router\Types\NodeType;
use App\Services\Router\Types\CoordType;

class DispatcherController extends Controller
{
    public function index()
    {
        // Retrieve users who are employees (user_id exists in employees table)
        $employees = User::whereHas('employee')->get();

        // Fetch all distribution centers (RouterNodes with location_type as 'distribution_center')
        $distributionCenters = RouterNodes::where('location_type', 'distribution_center')->get();

        // Fetch all cities
        $cities = City::all();

        // Pass data to the view
        return view('employees.dispatcher', [
            'employees' => $employees,
            'distributionCenters' => $distributionCenters,
            'cities' => $cities,
        ]);
    }

    public function getDistributionCenterDetails($id)
    {
        // Fetch the distribution center details
        $distributionCenter = RouterNodes::find($id);

        if (!$distributionCenter) {
            return response()->json(['error' => 'Distribution center not found'], 404);
        }

        // Fetch packages ready for delivery
        $packages = Package::whereHas('movements', function ($query) use ($id) {
            $query->where('current_node_id', $id)
                  ->whereNull('departure_time')
                  ->whereHas('destinationLocation', function ($subQuery) {
                      $subQuery->where('location_type', NodeType::ADDRESS);
                  });
        })->get();

        // Format the packages for the response
        $formattedPackages = $packages->map(function ($package) {
            try {
                $nextMovement = $package->getNextMovement();
                return [
                    'ref' => $package->reference,
                    'latitude' => $nextMovement->getLat(CoordType::DEGREE),
                    'longitude' => $nextMovement->getLong(CoordType::DEGREE),
                ];
            } catch (\Exception $e) {
                // Handle cases where movements are missing
                return [
                    'ref' => $package->reference,
                    'error' => 'No movements found for this package.',
                ];
            }
        });

        return response()->json([
            'distributionCenter' => $distributionCenter,
            'packages' => $formattedPackages,
        ]);
    }
}
