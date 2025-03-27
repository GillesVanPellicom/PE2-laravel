<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageMovement;
use App\Services\Router\Types\MoveOperationType;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourierController extends Controller
{
    //
    public function index()
    {
        return view('courier.index');
    }

    public function scan()
    {
        return view("courier.scan");
    }

    public function packages()
    {
        return view('courier.packages');
    }

    public function route()
    {
        return view('courier.route');
    }

    public function scanQr(Request $request)
    {
        $package = Package::find($request->package_id); // Find the package
        if (!$package) { // No package found
            return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => "This package does not exist."])->render()], 400);
        }

        $scannedPackages = session()->get('scanned_packages', []);
        if (!in_array($package->id, $scannedPackages)) {
            if (count($scannedPackages) >= 10) {
                array_shift($scannedPackages); // Remove the oldest package
            }
            $scannedPackages[] = $package->id; // Add the new package
            session()->put('scanned_packages', $scannedPackages); // Save the updated list
        }
        try {
        $package->generateMovements();
        } catch (Exception $e){
            return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Critical Error!", "message" => "Failed to generate a route for this package."])->render()], 500);
        };

        $mode = $request->mode; // Get the mode from the request
        if (!in_array($mode, ["INFO", "DELIVER", "IN", "OUT"])) {
            return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => "Invalid operating mode."])->render()], 400);

        }
        if ($mode == "INFO") { // If the user requested package info
            $currentMove = $package->movements()->where("current_node_id", $package->current_location_id)->first(); // Find the corresponding package movement
            $ref = $package->reference;
            $sender = $package->user->first_name . " " . $package->user->last_name;
            $reciever = $package->name . " " . $package->last_name;
            $phone = $package->receiver_phone_number;
            $weight = $package->weightClass->name;
            $dimension = $package->dimension;
            $from = LocationController::getAddressString($package->originLocation);
            $to = LocationController::getAddressString($package->destinationLocation);
            //$nextMove = $package->movements()->find($currentMove->next_movement);
            //$nextStop = is_null($currentMove->next_movement) ? LocationController::getAddressString($currentMove->node) : $currentMove;
            return response()->json(["success" => true, "message" => view('components.courier-modal', compact("ref", "sender", "reciever", "phone", "weight", "dimension", "from", "to"))->render()]);
        }

        try {
            [$status, $message] = $package->move(MoveOperationType::from($mode));
        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => $e->getMessage()])->render()], 500);

        }
        if ($status)
            return response()->json(["success" => true, "message" => $message]);
        return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => $message])->render()], 400);

        /*
        if ($package->current_location_id == $package->destination_location_id) { // If someone is trying to do an action on a delivered package
            return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => "This package has already reached its final location."])->render()], 400);
        }

        if (!$currentMove) { // If no package movement exists.
            return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Critical Error!", "message" => "This package does not have a valid package movement."])->render()], 500);
        }

        if ($mode == "DELIVER") { // If a package has to be delivered to a customeres home
            if ($package->destinationLocation->location_type != "Private Individu") { // If the destination is not a customers home
                return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => "This package does not have to be delivered to a Private Individu."])->render()], 400);
            }

            if ($package->destination_location_id != $currentMove->to_location_id) { // If a package is not yet ready to be delivered
                return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => "This package has not yet reached its final destination."])->render()], 400);
            }

            if ($currentMove->check_out_time == null) { // If the package hasnt been checked out (Previous locations fault)
                return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => "The previous person failed to scan this package out."])->render()], 400);

            } else if ($currentMove->departure_time == null) { // If the package hasnt been checked in (Drivers fault)
                return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => "You did not check this package in."])->render()], 400);

            } else if ($currentMove->arrival_time == null) { // If the package can be delivered
                $currentMove->arrival_time = Carbon::now();
                $currentMove->check_in_time = Carbon::now();
                $package->current_location_id = $currentMove->to_location_id;
                $currentMove->save();
                $package->save();
                return response()->json(["success" => true, "message" => "Package succesfully delivered "]);

            } else { // Package has already been delivered or something went wrong
                return response()->json(["success" => false, "message" => "Package already delivered "], 400);
            }
        }

        // Correctly update the right field
        $appliedMode = null;
        if ($currentMove->check_out_time == null) {
            $currentMove->check_out_time = Carbon::now();
            $appliedMode = "OUT";

        } else if ($currentMove->departure_time == null) {
            $currentMove->departure_time = Carbon::now();
            $appliedMode = "IN";

        } else if ($currentMove->arrival_time == null) {
            $currentMove->arrival_time = Carbon::now();
            $appliedMode = "OUT";

        } else if ($currentMove->check_in_time == null) {
            $currentMove->check_in_time = Carbon::now();
            $package->current_location_id = $currentMove->to_location_id; // Move package to next package movement
            $appliedMode = "IN";

        } else { // Package is still in his previous package movement but has already been scanned 4 times, Critical fault
            return response()->json(['success' => false, 'message' => 'Something went wrong trying to process this request.'], 500);
        }

        if ($appliedMode === $mode) { // If the applied mode is the requested mode, save
            $currentMove->save();
            $package->save();
            return response()->json(["success" => true, "message" => "Package succesfully scanned " . $mode]);

        } else {
            return response()->json(['success' => false, 'message' => 'This package was not previously scanned ' . ($mode == "OUT" ? "in" : "out") . "."], 400);
        }
            */
    }


    public function getLastPackages()
    {
        return view('components.courier-card');
    }
}
