<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\RouterNodes;
use App\Services\Router\Types\Node;
use App\Services\RouteTracer\RouteTrace;
use App\Services\Router\Types\NodeType;
use Illuminate\Http\Request;
use App\Services\Router\Types\CoordType;
use App\Models\Location;

class CourierRouteController extends Controller
{
    public function showRoute()
    {
        $packages = Package::all();
        $filteredPackages = [];
        foreach ($packages as $package) {
            if (!$package->movements()->exists())
                continue;

            $currentMovement = $package->movements()->where("current_node_id", $package->current_location_id)->first(); // Find the corresponding package movement
            $movement = is_null($currentMovement->next_movement) ? $currentMovement : $currentMovement->nextHop;

            if (is_numeric($movement->current_node_id) && $movement->node->location_type == NodeType::ADDRESS && is_null($movement->arrival_time)) {
                $node = Node::fromLocation($movement->node);
                $filteredPackages[] = [
                    'latitude' => $node->getLat(CoordType::DEGREE),
                    'longitude' => $node->getLong(CoordType::DEGREE),
                    'ref' => $package->reference,
                    'end' => $movement->id == $currentMovement->id
                ];
            }
            /*
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
                */
        }

        $routeTracer = new RouteTrace();
        $route = $routeTracer->generateRoute($filteredPackages);

        return view('courier.route', compact('route'));
    }
}