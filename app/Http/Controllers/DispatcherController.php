<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RouterNodes;
use App\Models\City;
use App\Models\Package;
use App\Services\Router\Types\NodeType;
use App\Services\Router\Types\CoordType;
use App\Services\RouteTracer\RouteTrace;

class DispatcherController extends Controller
{
    public function index()
    {
        // Get active couriers with their user information
            $couriers = User::select('users.*', 'employees.id as employee_id')
                ->join('employees', 'users.id', '=', 'employees.user_id')
                ->join('contracts', 'employees.id', '=', 'contracts.employee_id')
                ->join('functions', 'contracts.job_id', '=', 'functions.id')
                ->where('functions.name', 'LIKE', '%courier%')
                ->where(function($q) {
                    $q->where('contracts.end_date', '>=', now())
                    ->orWhereNull('contracts.end_date');
                })
                ->get();
    
        $distributionCenters = RouterNodes::where('location_type', 'DISTRIBUTION_CENTER')->get();
        $cities = City::all();
        
        return view('employees.dispatcher', [
            'employees' => $couriers,
            'distributionCenters' => $distributionCenters,
            'cities' => $cities,
        ]);
    }

    public function getDistributionCenterDetails(Request $request, $id)
    {
        try {
            $dcId = $id;
            \Log::info("Fetching DC details for: " . $dcId);
    
            // Get packages at this distribution center with their movements
            $packages = Package::select(
                'packages.id',
                'packages.reference',
                'current_rn.id as dc_id',
                'current_rn.description as dc_description',
                'pm.id as movement_id',
                'pm.current_node_id',
                'next_pm.handled_by_courier_id',
                'next_pm.current_node_id as next_node_id',
                'next_rn.description as next_node_description',
                'next_rn.city_id as next_city_id',
                'cities.name as next_city_name',
                'locations.description as destination_description',
                'users.first_name as courier_first_name',
                'users.last_name as courier_last_name',
                'next_rn.description as next_movement_description'
            )
            ->join('package_movements as pm', 'packages.id', '=', 'pm.package_id')
            ->join('router_nodes as current_rn', 'pm.current_node_id', '=', 'current_rn.id')
            ->leftJoin('package_movements as next_pm', 'pm.next_movement', '=', 'next_pm.id')
            ->leftJoin('router_nodes as next_rn', 'next_pm.current_node_id', '=', 'next_rn.id')
            ->leftJoin('cities', 'next_rn.city_id', '=', 'cities.id')
            ->leftJoin('locations', 'packages.destination_location_id', '=', 'locations.id')
            ->leftJoin('employees', 'next_pm.handled_by_courier_id', '=', 'employees.id')  // Changed this join
            ->leftJoin('users', 'employees.user_id', '=', 'users.id')
            ->where('current_rn.id', '=', $dcId)
            ->where('current_rn.location_type', '=', 'DISTRIBUTION_CENTER')
            ->orderBy('cities.name')
            ->orderBy('pm.created_at', 'DESC')
            ->get();
    
            // Group packages by assignment status and region
            $assignedPackages = $packages->filter(function ($package) {
                return !is_null($package->handled_by_courier_id);
            })->groupBy('next_city_name');
    
            $unassignedPackages = $packages->filter(function ($package) {
                return is_null($package->handled_by_courier_id);
            })->groupBy('next_city_name');
    
            $response = [
                'success' => true,
                'distributionCenter' => [
                    'id' => $dcId,
                    'description' => $packages->first()->dc_description ?? 'Unknown'
                ],
                'unassignedGroups' => $unassignedPackages->map(function ($groupPackages, $cityName) {
                    $nextMovementDesc = $groupPackages->first()->next_movement_description;
                    return [
                        'city' => $cityName ?? 'Unknown',
                        'nextMovement' => $nextMovementDesc ?? 'Unknown',
                        'packages' => $groupPackages->map(function ($package) {
                            return [
                                'ref' => $package->reference,
                                'id' => $package->id,
                                'destination' => $package->destination_description ?? 'Unknown',
                                'next_node' => $package->next_node_description ?? 'Unknown'
                            ];
                        })->values()->all()
                    ];
                })->values()->all(),
                'assignedGroups' => $assignedPackages->map(function ($groupPackages, $cityName) {
                    return [
                        'city' => $cityName ?? 'Unknown',
                        'nextMovement' => $groupPackages->first()->next_movement_description ?? 'Unknown',
                        'packages' => $groupPackages->map(function ($package) {
                            return [
                                'ref' => $package->reference,
                                'id' => $package->id,
                                'destination' => $package->destination_description ?? 'Unknown',
                                'next_node' => $package->next_node_description ?? 'Unknown',
                                'courier' => trim($package->courier_first_name . ' ' . $package->courier_last_name) ?: 'Unassigned'
                            ];
                        })->values()->all()
                    ];
                })->values()->all()
            ];
    
            return response()->json($response);
    
        } catch (\Exception $e) {
            \Log::error("Error in getDistributionCenterDetails:", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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

            // First check if courier already has packages assigned in a different region
            $courierCurrentAssignments = \DB::table('package_movements as pm')
            ->join('router_nodes as rn', 'pm.current_node_id', '=', 'rn.id')
            ->join('cities as c', 'rn.city_id', '=', 'c.id')
            ->where('pm.handled_by_courier_id', $employeeId)
            ->whereNull('pm.departure_time')
            ->select('c.name as city_name', 'rn.description as node_description')
            ->distinct()
            ->get();

            // Get the region of the packages being assigned
            $newPackageMovement = \DB::table('packages as p')
                ->join('package_movements as pm', 'p.id', '=', 'pm.package_id')
                ->join('package_movements as next_pm', 'pm.next_movement', '=', 'next_pm.id')
                ->join('router_nodes as rn', 'pm.current_node_id', '=', 'rn.id')
                ->join('cities as c', 'rn.city_id', '=', 'c.id')
                ->whereIn('p.reference', $packageRefs)
                ->select('c.name as city_name', 'rn.description as node_description')
                ->first();
               
            // Check if courier already has packages in a different region
            if ($courierCurrentAssignments->isNotEmpty()) {
                $currentAssignment = $courierCurrentAssignments->first();
                if ($currentAssignment->node_description !== $newPackageMovement->node_description) {
                    return response()->json([
                        'success' => false,
                        'message' => "Courier already has assignments to {$currentAssignment->node_description}. Cannot assign packages from different loadout."
                    ], 400);
                }
            }

            // Verify employee is a courier through contract
            $isCourier = \App\Models\Employee::where('id', $employeeId)
                ->whereHas('contracts', function($query) {
                    $query->join('functions', 'contracts.job_id', '=', 'functions.id')
                        ->where('functions.name', 'LIKE', '%courier%')
                        ->where(function($q) {
                            $q->where('contracts.end_date', '>=', now())
                            ->orWhereNull('contracts.end_date');
                        });
                })
                ->exists();

            if (!$isCourier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected employee is not an active courier'
                ], 400);
            }

            \DB::beginTransaction();

            $updatedCount = 0;
            foreach ($packageRefs as $ref) {
                $package = Package::where('reference', $ref)->first();
                if (!$package) continue;

                $currentMovement = $package->movements()
                    ->whereNull('departure_time')
                    ->whereNull('check_in_time')
                    ->first();

                if ($currentMovement && $currentMovement->next_movement) {
                    $nextMovement = $package->movements()
                        ->where('id', $currentMovement->next_movement)
                        ->first();

                    if ($nextMovement) {
                        $nextMovement->handled_by_courier_id = $employeeId;
                        $nextMovement->save();
                        
                        \Log::info("Updated package movement", [
                            'package_ref' => $ref,
                            'movement_id' => $nextMovement->id,
                            'employee_id' => $employeeId
                        ]);
                        
                        $updatedCount++;
                    }
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "$updatedCount packages dispatched successfully",
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

    private $routeTrace;

    public function __construct()
    {
        $this->routeTrace = new RouteTrace();
    }

    // Add this new method
    public function calculateOptimalSelection(Request $request)
    {
        try {
            $packages = Package::whereIn('reference', $request->input('packages', []))
                ->join('locations', 'packages.destination_location_id', '=', 'locations.id')
                ->select(
                    'packages.reference',
                    'locations.latitude',
                    'locations.longitude'
                )
                ->get()
                ->map(function ($package) {
                    return [
                        'reference' => $package->reference,
                        'latitude' => $package->latitude,
                        'longitude' => $package->longitude
                    ];
                })
                ->toArray();
                $optimalRoute = $this->routeTrace->generateRoute($packages);
            
            return response()->json([
                'success' => true,
                'packages' => array_map(function($package) {
                    return $package['reference'];
                }, $optimalRoute)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate optimal selection: ' . $e->getMessage()
            ], 500);
        }
    }
}

