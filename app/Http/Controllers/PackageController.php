<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::paginate(10);
        return view('pickup.dashboard', compact('packages'));
    }
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

    public function generateQRcode($packageID){
        $qrCode = QrCode::size(300)->generate($packageID);
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}
