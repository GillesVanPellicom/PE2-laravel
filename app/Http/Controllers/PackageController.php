<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PackageController extends Controller
{
    public function updateStatus(Request $request)
    {
        $package = Package::where('id', $request->packageId)->first();

        if ($package) {
            $package->status = $request->status;
            $package->save();
<<<<<<< HEAD

            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Package not found']);
    }

=======
    
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
    
>>>>>>> ffdcb88737ee4e79a4f1f9df7937232efde3c71c
    public function generateQRcode($packageID){
        $qrCode = QrCode::size(300)->generate($packageID);
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> ffdcb88737ee4e79a4f1f9df7937232efde3c71c
