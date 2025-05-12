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
use App\Models\PackageMovement;

class CourierRouteController extends Controller
{
    public function showRoute()
    {
        // Haal de ingelogde courier op
        
        $courierId = auth()->user()->employee->id;
        $courierRoute = auth()->user()->employee->courierRoute;
        //dd($courierRoute);
        $start = $courierRoute->currentLocation();
        $end = $courierRoute->endLocation() ?? $courierRoute->startLocation();

        // Haal alleen de pakketten op die zijn toegewezen aan de ingelogde courier
        $route = [];
        $packagemovements = PackageMovement::where('handled_by_courier_id', $courierId)->where('departure_time', null)->get();
        foreach ($packagemovements as $packagemovement) {
            $package = Package::where('id', $packagemovement->package_id)->first();
            $movement = ($packagemovement->next_movement == null) ? $packagemovement : $packagemovement->nextHop;
            $location = Node::fromId($packagemovement->current_node_id);
            $route[] = [
                'latitude' => $location->getLat(CoordType::DEGREE),
                'longitude' => $location->getLong(CoordType::DEGREE),
                'ref' => $package->reference,
                'address' => $location ? [
                    "street" => $location->getAddress()->street,
                    "house_number" => $location->getAddress()->house_number,
                ] : null,
                'end' => $movement->id == $packagemovement->id,
                'requires_signature' => $package->requires_signature,
            ];
        }
        $start_coords = null;
        $end_coords = null;
        if ($start != null && $end != null){
            $start_coords = [$start->getLat(CoordType::DEGREE), $start->getLong(CoordType::DEGREE)];
            $end_coords = [$end->getLat(CoordType::DEGREE), $end->getLong(CoordType::DEGREE)];
        }
        return view('courier.route', compact('route', 'start_coords', 'end_coords'));
    }

    public function signature($id)
    {
        return view('courier.signature', ['id' => $id]);
    }

    public function deliver($id)
    {
        try {
            $package = Package::where('reference', $id)->firstOrFail();

            [$success, $message] = $package->move(MoveOperationType::DELIVER);

            if (!$success) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }

            $route = session('current_route', []);

            $route = array_filter($route, fn($item) => $item['ref'] !== $id);
            session(['current_route' => $route]);

            return response()->json(['success' => true, 'message' => $message, 'route' => $route]);
        } catch (\Exception $e) {
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

            $signature = $request->input('signature');
            $packageId = $request->input('package_id');

            $package = Package::where('reference', $packageId)->firstOrFail();

            [$success, $message] = $package->move(MoveOperationType::DELIVER);

            if (!$success) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }

            return response()->json(['success' => true, 'message' => 'Signature submitted and ' . $message]);
        } catch (\Exception $e) {
            \Log::error('Error in submitSignature: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'An error occurred while processing the request.'], 500);
        }
    }
}