<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
        $distributionCenters = RouterNodes::where('location_type', 'DISTRIBUTION_CENTER')->get();
        $cities = City::all();
        
        return view('employees.dispatcher', [
            'employees' => $employees,
            'distributionCenters' => $distributionCenters,
            'cities' => $cities,
            'distributionCenter' => null, // Add this line
            'readyToDeliverPackages' => collect([]),
            'inStockPackages' => collect([]),
        ]);
    }

    public function getDistributionCenterDetails(Request $request, $id)
    {
        try {
            $dcId = $id;  // ID komt al in juiste format binnen (@DC_ANTWERP)
            \Log::info("Fetching DC details for: " . $dcId);
    
            // Get all packages for this DC using the working query structure
            $packages = Package::select(
                'packages.id',
                'packages.reference',
                'router_nodes.id as dc_id',
                'router_nodes.description as dc_description',
                'package_movements.current_node_id',
                'package_movements.arrival_time',
                'package_movements.departure_time',
                'package_movements.check_in_time',
                'locations.description as destination_description'
            )
            ->join('package_movements', 'packages.id', '=', 'package_movements.package_id')
            ->join('router_nodes', 'package_movements.current_node_id', '=', 'router_nodes.id')
            ->leftJoin('locations', 'packages.destination_location_id', '=', 'locations.id')
            ->where('router_nodes.id', '=', $dcId)
            ->where('router_nodes.location_type', '=', 'DISTRIBUTION_CENTER')
            ->orderBy('package_movements.created_at', 'DESC')
            ->get();
    
            // Split packages into ready to deliver and in stock (aangepaste condities)
            // In de getDistributionCenterDetails method:

            // Split packages into ready to deliver and in stock
            $readyToDeliver = $packages->filter(function ($package) {
                return $package->arrival_time !== null     // Package has arrived
                    && $package->departure_time === null   // Package hasn't departed yet
                    && $package->check_in_time === null;   // Package hasn't been checked in yet
            });

            $inStock = $packages->filter(function ($package) {
                return $package->check_in_time !== null    // Package has been checked in
                    && $package->departure_time === null;  // Package hasn't departed yet
            });
    
            \Log::info("Filtered packages:", [
                'total' => $packages->count(),
                'ready' => $readyToDeliver->count(),
                'stock' => $inStock->count()
            ]);
    
            $response = [
                'success' => true,
                'distributionCenter' => [
                    'id' => $dcId,
                    'description' => $packages->first()->dc_description ?? 'Unknown'
                ],
                'readyToDeliver' => $readyToDeliver->map(function ($package) {
                    return [
                        'ref' => $package->reference,
                        'id' => $package->id,
                        'destination' => $package->destination_description ?? 'Unknown',
                        'status' => 'Ready for dispatch',
                        'arrival_time' => $package->arrival_time
                    ];
                })->values()->all(),
                'inStock' => $inStock->map(function ($package) {
                    return [
                        'ref' => $package->reference,
                        'id' => $package->id,
                        'nextDestination' => $package->destination_description ?? 'Unknown',
                        'status' => 'In Stock',
                        'check_in_time' => $package->check_in_time
                    ];
                })->values()->all()
            ];
    
            \Log::info("Response data:", [
                'ready_count' => count($response['readyToDeliver']),
                'stock_count' => count($response['inStock'])
            ]);
    
            return response()->json($response, 200, ['Content-Type' => 'application/json']);
    
        } catch (\Exception $e) {
            \Log::error("Error in getDistributionCenterDetails:", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => 'Failed to fetch data: ' . $e->getMessage()], 500);
        }
    }

    public function dispatchSelectedPackages(Request $request)
{
    try {
        $packageRefs = $request->input('packages', []);
        $employeeId = $request->input('employee_id');
        
        \Log::info("Dispatching packages:", [
            'refs' => $packageRefs,
            'employee_id' => $employeeId
        ]);

        // First check if the employee has a courier record
        $courier = \App\Models\Courier::where('employee_id', $employeeId)->first();
        
        if (!$courier) {
            return response()->json([
                'success' => false,
                'message' => 'Selected employee is not registered as a courier'
            ], 400);
        }

        \DB::beginTransaction();

        $packages = Package::whereIn('reference', $packageRefs)
            ->whereHas('movements', function ($query) {
                $query->whereNotNull('arrival_time')
                    ->whereNull('departure_time')
                    ->whereNull('check_in_time');
            })
            ->with('movements')
            ->get();

        $updatedCount = 0;
        foreach ($packages as $package) {
            $latestMovement = $package->movements()->latest()->first();
            if ($latestMovement) {
                $latestMovement->departure_time = now();
                $latestMovement->handled_by_courier_id = $courier->id; // Use courier ID instead of employee ID
                $latestMovement->save();
                
                $package->status = 'DISPATCHED';
                $package->save();
                
                $updatedCount++;
            }
        }

        \DB::commit();

        return response()->json([
            'success' => true,
            'message' => "$updatedCount packages dispatched successfully to courier",
            'updated_count' => $updatedCount
        ]);
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error("Error dispatching packages:", [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to dispatch packages: ' . $e->getMessage()
        ], 500);
    }
}

    public function processSelectedPackages(Request $request)
    {
        try {
            $packageRefs = $request->input('packages', []);
            \Log::info("Processing packages:", ['refs' => $packageRefs]);

            // Update package statuses in bulk using the correct relationship name
            $updatedCount = Package::whereIn('reference', $packageRefs)
                ->whereHas('movements', function ($query) {  // Changed from package_movements to movements
                    $query->whereNotNull('check_in_time')
                        ->whereNull('departure_time');
                })
                ->update(['status' => 'PROCESSED']);

            return response()->json([
                'success' => true,
                'message' => "$updatedCount packages processed successfully",
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            \Log::error("Error processing packages:", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to process packages: ' . $e->getMessage()
            ], 500);
        }
    }
}