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

                // Fetch the location and its related address
                $location = Location::with('address')->where('id', $movement->current_node_id)->first();

                $filteredPackages[] = [
                    'latitude' => $node->getLat(CoordType::DEGREE),
                    'longitude' => $node->getLong(CoordType::DEGREE),
                    'ref' => $package->reference,
                    'end' => $movement->id == $currentMovement->id,
                    'address' => $location ? [
                        'street' => $location->address->street ?? 'N/A',
                        'house_number' => $location->address->house_number ?? 'N/A',
                    ] : null,
                ];
            }
        }

        $routeTracer = new RouteTrace();
        $route = $routeTracer->generateRoute($filteredPackages);

        return view('courier.route', compact('route'));
    }
}