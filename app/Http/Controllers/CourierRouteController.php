<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\RouterNodes;
use App\Services\RouteTracer\RouteTrace;
use App\Services\Router\Types\NodeType;
use Illuminate\Http\Request;
use App\Services\Router\Types\CoordType;

class CourierRouteController extends Controller
{
    public function showRoute()
    {
        // Retrieve all packages
        $packages = Package::all();
        $filteredPackages = [];
        foreach ($packages as $package) {
            // Get the last movement of the package
            $lastMovement = $package->getCurrentMovement();
            $nextMovement = $package->getNextMovement();

            if ($nextMovement->getType() === NodeType::ADDRESS && $lastMovement->getType() === NodeType::DISTRIBUTION_CENTER) {
                // Get the second-to-last movement
                $movements = $package->movements()->orderBy('id', 'desc')->get();
                $secondToLastMovement = $movements->skip(1)->first();
                if ($secondToLastMovement) {
                    $routerNode = RouterNodes::find($secondToLastMovement->current_node_id);

                        $filteredPackages[] = [
                            'latitude' => $nextMovement->getLat(CoordType::DEGREE),
                            'longitude' => $nextMovement->getLong(CoordType::DEGREE),
                            'ref' => $package->reference,
                        ];
                    
                }
            }
        }

        // Use RouteTracer to calculate the best route
        $routeTracer = new RouteTrace();
        $route = $routeTracer->generateRoute($filteredPackages);

        // Pass the route to the view
        return view('courier.route', compact('route'));
    }
}