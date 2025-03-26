<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Services\Router\Router;
use App\Services\Router\Types\Node;
use App\Services\Router\Types\NodeType;
use App\Services\Router\Types\CoordType;
use App\Services\Router\Types\MoveOperationType;


// use Illuminate\Support\Facades\App;

class TrackPackageController extends Controller
{
    public function track($reference)
    {
        $package = Package::where('reference', $reference)->firstOrFail();
            //->with('movements.toLocation', 'movements.fromLocation')
            //->firstOrFail();

        $movements = $package->getMovements();

        $currentLocation = $package->getCurrentMovement();

        foreach ($movements as $movement) {
            if ($movement->getID() === $currentLocation->getID()) {
                $movement->status = 'current'; 
            } elseif ($movement->getArrivedAt() && $movement->getCheckedOutAt()) {
                $movement->status = 'completed'; 
            } elseif ($movement->getDepartedAt() && !$movement->getArrivedAt()) {
                $movement->status = 'in_transit';
            } else {
                $movement->status = 'upcoming'; 
            }
        }

        return view('Track_App.track', compact('package', 'movements', 'currentLocation'))
            ->with('currentLat', $currentLocation ? $currentLocation->getLat(CoordType::DEGREE) : null)
            ->with('currentLng', $currentLocation ? $currentLocation->getLong(CoordType::DEGREE) : null);
    }

    public function deliverPackage($reference)
    {
        $package = Package::where('reference', $reference)
            ->firstOrFail();

        [$status, $message] = $package->move(MoveOperationType::DELIVER);
        return response()->json(["success" => $status, "message" => $message]);
    }
}
