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
        $packages = Package::all();
        $filteredPackages = [];
        foreach ($packages as $package) {
            $lastMovement = $package->getCurrentMovement();
            $nextMovement = $package->getNextMovement();

            if ($nextMovement->getType() === NodeType::ADDRESS && $lastMovement->getType() === NodeType::DISTRIBUTION_CENTER) {
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

        $routeTracer = new RouteTrace();
        $route = $routeTracer->generateRoute($filteredPackages);

        return view('courier.route', compact('route'));
    }
}