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

        // Fetch packages ready to deliver to addresses
        $readyToDeliverPackages = Package::whereHas('movements', function ($query) use ($id) {
            $query->where('current_node_id', $id)
                  ->whereNull('departure_time') // Not yet departed
                  ->whereHas('destinationLocation', function ($subQuery) {
                      $subQuery->where('location_type', NodeType::ADDRESS); // Final destination is an address
                  });
        })->get();

        // Fetch packages in stock (not ready to deliver, but still in the distribution center)
        $inStockPackages = Package::whereHas('movements', function ($query) use ($id) {
            $query->where('current_node_id', $id)
                  ->whereNull('departure_time') // Not yet departed
                  ->whereHas('destinationLocation', function ($subQuery) {
                      $subQuery->where('location_type', '!=', NodeType::ADDRESS); // Destination is not an address
                  });
        })->get();

        // Format the packages for the response
        $formattedReadyToDeliver = $readyToDeliverPackages->map(function ($package) {
            return [
                'ref' => $package->reference,
                'destination' => $package->destinationLocation->description ?? 'Unknown',
            ];
        });

        $formattedInStock = $inStockPackages->map(function ($package) {
            return [
                'ref' => $package->reference,
                'nextDestination' => $package->destinationLocation->description ?? 'Unknown',
            ];
        });

        return response()->json([
            'distributionCenter' => $distributionCenter,
            'readyToDeliver' => $formattedReadyToDeliver,
            'inStock' => $formattedInStock,
        ]);
    }
}
