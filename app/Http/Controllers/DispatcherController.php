<?php

namespace App\Http\Controllers;

use App\Models\Employee;
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
        $couriers = User::role("courier")
            ->join("employees", "employees.user_id", "=", "users.id")
            ->join("contracts", "contracts.employee_id", "=", "employees.id")
            ->join("functions", "contracts.job_id", "=", "functions.id")
            ->where("functions.name", "LIKE", "%courier%")
            ->where(function($q) {
                $q->where('contracts.end_date', '>=', now())
                ->orWhereNull('contracts.end_date');
            })
            // check if courier is not assigned to any active packages
            ->whereNotExists(function($query) {
                $query->select('package_movements.handled_by_courier_id')
                    ->from('package_movements')
                    ->whereNull('departure_time')
                    ->whereColumn('package_movements.handled_by_courier_id', 'employees.id');
            })
            ->select('employees.id as employee_id', 'users.first_name', 'users.last_name', 'users.id as user_id')
            ->distinct()
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
    
            $packages = Package::select(
                'packages.id',
                'packages.reference',
                'packages.current_location_id',
                'packages.status',
                'current_rn.id as dc_id',
                'current_rn.description as dc_description',
                'pm.id as movement_id',
                'pm.current_node_id',
                'next_pm.handled_by_courier_id',
                'pm.arrival_time',
                'next_rn.description as next_node_description',
                'next_rn.city_id as next_city_id',
                'cities.name as next_city_name',
                'locations.description as destination_description',
                'users.first_name as courier_first_name',
                'users.last_name as courier_last_name',
                'next_rn.description as next_movement_description'
            )
            ->join('package_movements as pm', function($join) {
                $join->on('packages.id', '=', 'pm.package_id')
                    ->whereNotNull('pm.arrival_time')
                    ->whereNull('pm.departure_time');
            })
            ->join('router_nodes as current_rn', 'pm.current_node_id', '=', 'current_rn.id')
            ->leftJoin('package_movements as next_pm', 'pm.next_movement', '=', 'next_pm.id')
            ->leftJoin('router_nodes as next_rn', 'next_pm.current_node_id', '=', 'next_rn.id')
            ->leftJoin('cities', 'next_rn.city_id', '=', 'cities.id')
            ->leftJoin('locations', 'packages.destination_location_id', '=', 'locations.id')
            ->leftJoin('employees', 'next_pm.handled_by_courier_id', '=', 'employees.id')
            ->leftJoin('users', 'employees.user_id', '=', 'users.id')
            ->where('pm.current_node_id', '=', $dcId)
            ->where('current_rn.location_type', '=', 'DISTRIBUTION_CENTER')
            ->where('packages.status', '=', 'pending')
            ->orderBy('pm.arrival_time', 'ASC')
            ->orderBy('cities.name')
            ->get();
    
            // Split packages based on next movement assignment
            $assignedPackages = $packages->filter(function ($package) {
                return !is_null($package->handled_by_courier_id);
            })->groupBy('next_city_name');
    
            $unassignedPackages = $packages->filter(function ($package) {
                return is_null($package->handled_by_courier_id);
            })->groupBy('next_city_name');
    
            \Log::info("Groups created:", [
                'assigned_count' => $assignedPackages->count(),
                'unassigned_count' => $unassignedPackages->count()
            ]);
    
            $response = [
                'success' => true,
                'distributionCenter' => [
                    'id' => $dcId,
                    'description' => $packages->first()->dc_description ?? 'Unknown'
                ],
                'unassignedGroups' => $unassignedPackages->map(function ($groupPackages, $cityName) {
                    return [
                        'city' => $cityName ?? 'Home Delivery',
                        'nextMovement' => $groupPackages->first()->next_movement_description ?? 'Home Delivery',
                        'packages' => $groupPackages->map(function ($package) {
                            return [
                                'ref' => $package->reference,
                                'id' => $package->id,
                                'destination' => $package->destination_description ?? 'Home Address',
                                'next_node' => $package->next_node_description ?? 'Home Delivery'
                            ];
                        })->values()->all()
                    ];
                })->values()->all(),
                'assignedGroups' => $assignedPackages->map(function ($groupPackages, $cityName) {
                    return [
                        'city' => $cityName ?? 'Home Delivery',
                        'nextMovement' => $groupPackages->first()->next_movement_description ?? 'Home Delivery',
                        'packages' => $groupPackages->map(function ($package) {
                            return [
                                'ref' => $package->reference,
                                'id' => $package->id,
                                'destination' => $package->destination_description ?? 'Home Address',
                                'next_node' => $package->next_node_description ?? 'Home Delivery',
                                'courier' => trim($package->courier_first_name . ' ' . $package->courier_last_name) ?: 'Unassigned'
                            ];
                        })->values()->all()
                    ];
                })->values()->all()
            ];
    
            \Log::info("Response prepared:", $response);
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
            
            // Get package locations and calculate total route distance
            $packages = Package::whereIn('reference', $packageRefs)
                ->join('locations', 'packages.destination_location_id', '=', 'locations.id')
                ->select(
                    'packages.reference',
                    'locations.latitude',
                    'locations.longitude',
                    'packages.weight_id',
                )
                ->get()
                ->toArray();

            $pack = Package::whereIn('reference', $packageRefs)->get();
            
            foreach ($pack as $package) {
                if($package->weight == NULL)
                {
                    $package->weight = round(mt_rand($package->weightClass->weight_min * 1000, $package->weightClass->weight_max * 1000) / 1000, 3);
                    $package->update(['weight' => $package->weight]);
                }
            }
    
            // Use RouteTrace to validate total distance
            $routeTracer = new RouteTrace();
            $route = $routeTracer->generateRoute($packages);
    
            if (empty($route)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not calculate valid route for these packages'
                ], 400);
            }

            // Check if packages are already assigned
            $alreadyAssigned = Package::whereIn('reference', $packageRefs)
                ->join('package_movements as pm', 'packages.id', '=', 'pm.package_id')
                ->join('package_movements as next_pm', 'pm.next_movement', '=', 'next_pm.id')
                ->whereNotNull('next_pm.handled_by_courier_id')
                ->exists();

            if ($alreadyAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more packages are already assigned to a courier'
                ], 400);
            }

            \DB::beginTransaction();

            $updatedCount = 0;
            foreach ($packageRefs as $ref) {
                $package = Package::where('reference', $ref)->first();
                if (!$package) continue;

                $currentMovement = $package->movements()
                    ->whereNull('departure_time')
                    ->first();

                if ($currentMovement && $currentMovement->nextHop) {
                    $currentMovement->nextHop->handled_by_courier_id = $employeeId;
                    $currentMovement->nextHop->save();
                    $updatedCount++;
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "$updatedCount packages assigned successfully"
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error in dispatchSelectedPackages:", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign packages: ' . $e->getMessage()
            ], 500);
        }
    }

    public function unassignPackages(Request $request)
    {
        try {
            $packageRefs = $request->input('packages', []);
            
            \Log::info("Unassigning packages:", [
                'refs' => $packageRefs
            ]);

            \DB::beginTransaction();

            $updatedCount = 0;
            foreach ($packageRefs as $ref) {
                $package = Package::where('reference', $ref)->first();
                if (!$package) continue;

                $currentMovement = $package->movements()
                    ->whereNull('departure_time')
                    ->whereNull('check_in_time')
                    ->first();

                if ($currentMovement) {
                    // Update the current movement instead of the next one
                    $currentMovement->handled_by_courier_id = null;
                    $currentMovement->save();
                    
                    \Log::info("Unassigned package movement", [
                        'package_ref' => $ref,
                        'movement_id' => $currentMovement->id
                    ]);
                    
                    $updatedCount++;
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "$updatedCount packages unassigned successfully",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error unassigning packages:", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to unassign packages: ' . $e->getMessage()
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
            // Get packages with their locations
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

            // Get starting point (distribution center)
            $distributionCenter = RouterNodes::find($request->input('dc_id'));
            if ($distributionCenter) {
                array_unshift($packages, [
                    'reference' => 'DC',
                    'latitude' => $distributionCenter->latDeg,
                    'longitude' => $distributionCenter->lonDeg
                ]);
            }

            // Use RouteTrace to calculate optimal route within 150km limit
            $routeTracer = new RouteTrace();
            $optimalRoute = $routeTracer->generateRoute($packages);

            // Remove DC from route if it was added
            $optimalRoute = array_filter($optimalRoute, function($package) {
                return $package['reference'] !== 'DC';
            });

            return response()->json([
                'success' => true,
                'packages' => array_map(function($package) {
                    return $package['reference'];
                }, $optimalRoute),
                'total_distance' => array_sum(array_map(function($package) {
                    return $package['distance'] ?? 0;
                }, $optimalRoute))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate optimal selection: ' . $e->getMessage()
            ], 500);
        }
    }
}