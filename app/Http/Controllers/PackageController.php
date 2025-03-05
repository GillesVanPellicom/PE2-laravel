<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;

class PackageController extends Controller
{
    public function updateStatus(Request $request)
    {
        $package = Package::where('id', $request->packageId)->first();

        if ($package) {
            $package->status = $request->status;
            $package->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Package not found']);
    }
}
