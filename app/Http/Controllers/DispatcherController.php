<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RouterNodes;
use App\Models\City;
use App\Models\Package;
use App\Models\PackageMovement;
use App\Services\Router\Helpers\GeoMath;
use App\Services\RouteTracer\RouteTrace;

class DispatcherController extends Controller
{
    public function index()
    {
        // Get active couriers with their user information
        $couriers = User::role("courier")
            ->whereHas('employee') // Controleer of de gebruiker een gekoppelde Employee heeft
            ->join("employees", "employees.user_id", "=", "users.id")
            ->join("contracts", "contracts.employee_id", "=", "employees.id")
            ->join("functions", "contracts.job_id", "=", "functions.id")
            ->where("functions.name", "LIKE", "%courier%")
            ->where(function($q) {
                $q->where('contracts.end_date', '>=', now())
                  ->orWhereNull('contracts.end_date');
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
    
            return response()->json($response)
                ->header('Content-Type', 'application/json')
                ->header('Access-Control-Allow-Origin', '*');
    
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

            if (empty($packageRefs) || !$employeeId) {
                return response()->json(['success' => false, 'message' => 'Invalid input data'], 400);
            }

            // Verwerk de pakketten en wijs ze toe aan de medewerker
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
                'message' => "$updatedCount packages assigned successfully",
                'route_distance' => 120.5 // Voeg hier de berekende afstand toe
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Dispatch error:', [
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
                
                // Zoek de huidige bewegingen van het pakket
                $currentMovement = $package->movements()
                    ->join('package_movements as next_pm', 'package_movements.next_movement', '=', 'next_pm.id')
                    ->whereNotNull('next_pm.handled_by_courier_id')
                    ->whereNull('package_movements.departure_time')
                    ->select('next_pm.id', 'next_pm.handled_by_courier_id')
                    ->first();
                    
                if ($currentMovement) {
                    // Update de reference naar de movement die een courier heeft toegewezen
                    $nextMovement = PackageMovement::find($currentMovement->id);
                    if ($nextMovement) {
                        $nextMovement->handled_by_courier_id = null;
                        $nextMovement->save();
                        
                        \Log::info("Unassigned package movement", [
                            'package_ref' => $ref,
                            'movement_id' => $nextMovement->id
                        ]);
                        
                        $updatedCount++;
                    }
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

    public function calculateOptimalSelection(Request $request)
    {
        try {
            $packages = Package::join('package_movements as pm', 'packages.id', '=', 'pm.package_id')
                ->where('pm.current_node_id', $request->input('dc_id'))
                ->whereNull('pm.departure_time')
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

    public function getCourierRoute($id)
    {
        try {
            \Log::info("Fetching courier route for courier ID: $id");

            // Controleer of de courier bestaat
            $courier = Employee::findOrFail($id);

            // Haal actieve pakketten op die aan deze courier zijn toegewezen
            $packages = Package::join('package_movements as pm', 'packages.id', '=', 'pm.package_id')
                ->leftJoin('package_movements as next_pm', 'pm.next_movement', '=', 'next_pm.id')
                ->leftJoin('router_nodes as next_node', 'next_pm.current_node_id', '=', 'next_node.id')
                ->leftJoin('locations as destination', 'packages.destination_location_id', '=', 'destination.id')
                ->where(function ($query) use ($id) {
                    $query->where('pm.handled_by_courier_id', $id)
                          ->orWhere('next_pm.handled_by_courier_id', $id);
                })
                ->whereNull('pm.departure_time')
                ->select(
                    'packages.id',
                    'packages.reference',
                    'pm.current_node_id as start_node_id',
                    'next_node.latDeg as next_latitude',
                    'next_node.lonDeg as next_longitude',
                    'next_node.description as next_description',
                    'destination.latitude as destination_latitude',
                    'destination.longitude as destination_longitude',
                    'destination.description as destination_description'
                )
                ->groupBy(
                    'packages.id',
                    'packages.reference',
                    'pm.current_node_id',
                    'next_node.latDeg',
                    'next_node.lonDeg',
                    'next_node.description',
                    'destination.latitude',
                    'destination.longitude',
                    'destination.description'
                )
                ->get();

            \Log::info("Packages fetched for courier ID $id:", $packages->toArray());

            if ($packages->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'route_distance' => 0,
                    'packages' => []
                ]);
            }

            // Haal de startpositie op van de eerste `current_node_id`
            $startNode = RouterNodes::where('id', $packages->first()->start_node_id)->first();

            if ($startNode) {
                $startPoint = [
                    'reference' => 'Start',
                    'latitude' => (float)$startNode->latDeg,
                    'longitude' => (float)$startNode->lonDeg,
                    'description' => $startNode->description
                ];
            } else {
                \Log::warning("Start node not found for courier ID $id.");
                $startPoint = null;
            }

            // Bereken de totale routeafstand vanaf de volgende nodes of bestemmingen
            $deliveryPoints = $packages->map(function ($package) {
                if (!is_null($package->next_latitude) && !is_null($package->next_longitude)) {
                    // Gebruik de volgende beweging als bestemming
                    return [
                        'reference' => $package->reference,
                        'latitude' => (float)$package->next_latitude,
                        'longitude' => (float)$package->next_longitude,
                        'description' => $package->next_description
                    ];
                } elseif (!is_null($package->destination_latitude) && !is_null($package->destination_longitude)) {
                    // Gebruik de uiteindelijke bestemming als er geen volgende beweging is
                    return [
                        'reference' => $package->reference,
                        'latitude' => (float)$package->destination_latitude,
                        'longitude' => (float)$package->destination_longitude,
                        'description' => $package->destination_description
                    ];
                } else {
                    \Log::warning("Package with reference {$package->reference} has no valid coordinates.");
                    return null;
                }
            })->filter()->toArray(); // Filter null-waarden uit de array

            \Log::info("Delivery points for courier ID $id:", $deliveryPoints);

            if (empty($deliveryPoints)) {
                return response()->json([
                    'success' => true,
                    'route_distance' => 0,
                    'packages' => []
                ]);
            }

            // Voeg de startpositie toe aan de route
            if ($startPoint) {
                array_unshift($deliveryPoints, $startPoint);
            }

            // Bereken de routeafstand
            $routeTracer = new RouteTrace();
            $route = $routeTracer->generateRoute($deliveryPoints);
            $totalDistance = 0;
            for ($i = 0; $i < count($route) - 1; $i++) {
                $distance = GeoMath::sphericalCosinesDistance(
                    deg2rad($route[$i]['latitude']),
                    deg2rad($route[$i]['longitude']),
                    deg2rad($route[$i + 1]['latitude']),
                    deg2rad($route[$i + 1]['longitude'])
                );
                $totalDistance += $distance;
            }

            return response()->json([
                'success' => true,
                'route_distance' => round($totalDistance, 2),
                'packages' => $packages
            ]);
        } catch (\Exception $e) {
            \Log::error("Error fetching courier route for courier ID $id:", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch courier route: ' . $e->getMessage()
            ], 500);
        }
    }
}