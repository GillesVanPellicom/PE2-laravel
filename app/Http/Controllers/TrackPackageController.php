<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Services\Router\Router;
use App\Services\Router\Types\Node;
// use Illuminate\Support\Facades\App;

class TrackPackageController extends Controller
{
    public function track($reference)
{
    // try{
    // $router = new Router();
    // }
    // catch (Exception $e){
    //     echo 'Caught exception: ',  $e->getMessage(), "\n";
    // }

   

    $package = Package::where('reference', $reference)
        ->with('movements.toLocation', 'movements.fromLocation')
        ->firstOrFail();

    // Sort movements
    $movements = $package->movements->sortBy('departure_time');

    // Determine current location and annotate each movement
    $currentLocation = $package->currentLocation;
    $foundCurrent = false;

    foreach ($movements as $movement) {
        if ($movement->check_in_time && !$movement->check_out_time && !$foundCurrent) {
            // Current location found
            $movement->status = 'current'; // Current location
            $currentLocation = $movement->toLocation;
            $foundCurrent = true;
        } elseif ($movement->check_in_time && $movement->check_out_time) {
            // Already handled and moved on
            $movement->status = 'completed';
        } elseif ($movement->departure_time && !$movement->check_in_time) {
            // In transit
            $movement->status = 'in_transit';
        } else {
            // Future (planned but not started)
            $movement->status = 'upcoming';
        }
    }

    return view('Track_App.track', compact('package', 'movements', 'currentLocation'))
    ->with('currentLat', $currentLocation ? $currentLocation->latitude : null)
    ->with('currentLng', $currentLocation ? $currentLocation->longitude : null);

    

}

}
