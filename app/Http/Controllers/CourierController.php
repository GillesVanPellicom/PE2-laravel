<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageMovement;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;

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
        $package = Package::find($request->package_id);
        if (!$package) {
            return response()->json(['success' => false, 'message' => 'Package not found'], 500);
        }

        $mode = $request->mode;
        $currentMove = PackageMovement::where('package_id', $package->id)->where("from_location_id", $package->current_location_id)->first();
        if ($mode == "INFO"){
            //This is a test commit
            return response()->json(["success" => true, "package" => $package]);
        }

        if ($package->current_location_id == $package->destination_location_id){
            return response()->json(['success' => false, 'message' => 'Package has reached its final destination'], 400);
        }

        if (!$currentMove){
            return response()->json(['success' => false, 'message' => 'PackageMovement not found'], 500);
        }

        if ($mode == "DELIVER"){
            if ($package->destinationLocation->location_type != "Private Individu"){
                return response()->json(['success' => false, 'message' => 'This package is not fit for delivery.'], 400);
            }
            if ($package->destination_location_id != $currentMove->to_location_id){
                return response()->json(['success' => false, 'message' => 'Destination does not match delivery address.'], 400);
            }
            if ($currentMove->check_out_time == null){
                return response()->json(['success' => false, 'message' => 'This package was not previously scanned OUT'], 400);
            } else if ($currentMove->departure_time == null){
                return response()->json(['success' => false, 'message' => 'This package was not previously scanned IN'], 400);
            } else if ($currentMove->arrival_time == null){
                $currentMove->arrival_time = Carbon::now();
                $currentMove->check_in_time = Carbon::now();
                $package->current_location_id = $currentMove->to_location_id;
                $currentMove->save();
                $package->save();
                return response()->json(["success" => true, "message" => "Package succesfully delivered "]);
            } else {
                return response()->json(["success" => false, "message" => "Package already delivered "], 400);
            }
        }

        $appliedMode = null;
        if ($currentMove->check_out_time == null){
            $currentMove->check_out_time = Carbon::now();
            $appliedMode = "OUT";
        } else if ($currentMove->departure_time == null){
            $currentMove->departure_time = Carbon::now();
            $appliedMode = "IN";
        } else if ($currentMove->arrival_time == null){
            $currentMove->arrival_time = Carbon::now();
            $appliedMode = "OUT";
        } else if ($currentMove->check_in_time == null){
            $currentMove->check_in_time = Carbon::now();
            $package->current_location_id = $currentMove->to_location_id;
            $appliedMode = "IN";
        } else {
            return response()->json(['success' => false, 'message' => 'Something went wrong trying to process this request.'], 500);
        }

        if ($appliedMode == $mode){
            $currentMove->save();
            $package->save();
            return response()->json(["success" => true, "message" => "Package succesfully scanned " . $mode]);
        } else {
            return response()->json(['success' => false, 'message' => 'This package was not previously scanned ' . ($mode == "OUT" ? "in" : "out") . "."], 400);
        }
    }
}
