<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageMovement;
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
            return response()->json(['success' => false, 'message' => 'Package not found']);
        }

        $mode = $request->mode;
        $currentMove = PackageMovement::where('package_id', $package->id)->where("from_location_id", $package->current_location_id)->first();
        switch ($mode) {
            case 'IN':
                return response()->json(["success" => true, "message" => "Package successfully scanned IN"]);

            case 'OUT':
                return response()->json(["success" => true, "message" => "Package successfully scanned OUT"]);

            case 'INFO':
                return response()->json(["success" => true, "message" => $currentMove->to_location_id]);

            default:
                return response()->json(['success' => false, 'message' => 'Invalid operating mode']);

        }
    }
}
