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
use Mockery\Expectation;

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
        $mode = $request->mode; // Get the mode from the request
        if (!in_array($mode, ["INFO", "DELIVER", "IN", "OUT", "UNDO", "RETURN", "FAILED"])) {
            return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => "Invalid operating mode."])->render()], 400);

        }

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
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Critical Error!", "message" => "Failed to generate a route for this package."])->render()], 500);
        }

        switch ($mode) {
            case 'INFO':
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

            case 'UNDO':
                $recentSuccess = session()->get('recent_success', []);
                if (!isset($recentSuccess[$package->id])) {
                    return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => "This scan cannot be undone."])->render()], 400);
                }
                $undoMode = $recentSuccess[$package->id];
                if ($undoMode == "IN" || $mode == "OUT") {
                    try {
                        [$status, $message] = $package->undoMove(MoveOperationType::from($undoMode));
                        if ($status) {
                            return response()->json(["success" => true, "message" => $message]);
                        }
                        return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => $message])->render()], 400);

                    } catch (Exception $e) {
                        return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => $e->getMessage()])->render()], 500);
                    }
                }
                return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => "You cannot undo a package delivery."])->render()], 400);

            case 'RETURN':
                try {
                    $package->return();
                    return response()->json(["success" => true, "message" => "Successfully marked as returning."]);
                } catch (Exception $e) {
                    return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => $e->getMessage()])->render()], 500);
                }
            case 'FAILED':
                try {
                    [$status, $message] = $package->failDelivery();
                    if ($status) {
                        return response()->json(["success" => true, "message" => $message]);
                    }
                    return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => $message])->render()], 400);
                
                } catch (Exception $e) {
                    return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => $e->getMessage()])->render()], 500);
                }            
            case "IN":
            case "OUT":
                try {
                    [$status, $message] = $package->move(MoveOperationType::from($mode));
                    if ($status) {
                        $successList = session()->get('recent_success', []);
                        $successList[$package->id] = $mode;
                        session()->put('recent_success', $successList);
                        return response()->json(["success" => true, "message" => $message]);
                    }
                    return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => $message])->render()], 400);
                
                } catch (Exception $e) {
                    return response()->json(['success' => false, 'message' => view('components.courier-error-modal', ["title" => "Something went wrong!", "message" => $e->getMessage()])->render()], 500);
                }
                
            default:
                break;
        }
    }


    public function getLastPackages()
    {
        return view('components.courier-card');
    }
}
