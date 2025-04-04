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
use App\Services\Router\Types\MoveOperationType;

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

    public function signature($id)
    {
        return view('courier.signature', ['id' => $id]);
    }

    public function deliver($id)
    {
        try {
            // Find the package by reference
            $package = Package::where('reference', $id)->firstOrFail();

            // Uncomment this when the `isSignature` field exists in the database
            // $package->isSignature = true;
            // $package->save();

            // Use the move() method to handle the delivery
            [$success, $message] = $package->move(MoveOperationType::DELIVER);

            if (!$success) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Error in deliver method: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'An error occurred while delivering the package.'], 500);
        }
    }

    public function submitSignature(Request $request)
    {
        try {
            $request->validate([
                'signature' => 'required',
                'package_id' => 'required',
            ]);

            // Save the signature (if needed)
            $signature = $request->input('signature');
            $packageId = $request->input('package_id');

            // Find the package by reference
            $package = Package::where('reference', $packageId)->firstOrFail();

            // Use the move() method to handle the delivery
            [$success, $message] = $package->move(MoveOperationType::DELIVER);

            if (!$success) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }

            return response()->json(['success' => true, 'message' => 'Signature submitted and ' . $message]);
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Error in submitSignature: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'An error occurred while processing the request.'], 500);
        }
    }
}