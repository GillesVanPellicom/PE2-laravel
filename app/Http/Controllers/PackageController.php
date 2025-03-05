<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;

class PackageController extends Controller
{
    
    public function updateStatus(Request $request)
    {
        // Ensure it's an AJAX request by checking the request headers
        if (!$request->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'Invalid request']);
        }
    
        // Use the correct column name for the package ID
        $package = Package::where('id', $request->packageId)->first(); // Use 'id' instead of 'package_id'
    
        if ($package) {
            $package->status = $request->status;
            $package->save();
    
            // Check if package is already in session to avoid duplicates
            $packageList = session('package_list', []);
    
            if (!in_array($package->id, $packageList)) {
                session()->push('package_list', $package->id);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'packageId' => $package->id
            ]);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'Package not found'
        ]);
    }
    

}
