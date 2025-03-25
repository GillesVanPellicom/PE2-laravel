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
        $employees = User::whereHas('employee')->get();

        $distributionCenters = RouterNodes::where('location_type', 'distribution_center')->get();

        $cities = City::all();

        return view('employees.dispatcher', [
            'employees' => $employees,
            'distributionCenters' => $distributionCenters,
            'cities' => $cities,
        ]);
    }

    public function getDistributionCenterDetails($id)
    {
        \Log::info("Fetching details for distribution center ID: $id");

        $distributionCenter = RouterNodes::find($id);

        if (!$distributionCenter) {
            \Log::error("Distribution center not found for ID: $id");
            return response()->json(['error' => 'Distribution center not found'], 404);
        }

        $readyToDeliverPackages = Package::whereHas('movements', function ($query) use ($id) {
            $query->where('current_node_id', $id)
                  ->whereNull('departure_time') 
                  ->whereHas('destinationLocation', function ($subQuery) {
                      $subQuery->where('location_type', NodeType::ADDRESS); 
                  });
        })->get();

        
        $inStockPackages = Package::whereHas('movements', function ($query) use ($id) {
            $query->where('current_node_id', $id)
                  ->whereNull('departure_time') 
                  ->whereHas('destinationLocation', function ($subQuery) {
                      $subQuery->where('location_type', '!=', NodeType::ADDRESS); 
                  });
        })->get();

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
