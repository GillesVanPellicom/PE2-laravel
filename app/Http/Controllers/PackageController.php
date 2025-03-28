<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\WeightClass;
use App\Models\DeliveryMethod;
use App\Models\Location;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\PackageCreatedMail;
use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;


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

    public function create()
    {
        $weightClasses = WeightClass::where('is_active', true)->get();
        $deliveryMethods = DeliveryMethod::where('is_active', true)->get();
        $locations = Location::all();
        
        return view('Packages.send-package', compact('weightClasses', 'deliveryMethods', 'locations'));
    }

    public function store(Request $request)
    {
        $userId = Auth::user()->id;

        $deliveryMethod = DeliveryMethod::findOrFail($request->delivery_method_id);
        $weightClass = WeightClass::findOrFail($request->weight_id);

        $validationRules = [
            'origin_location_id' => 'required|exists:locations,id',
            'destination_location_id' => 'exists:locations,id',
            'address_id' => 'exists:addresses,id',           
            'name' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'receiverEmail' => 'required|email|max:255',
            'receiver_phone_number' => 'required|string|max:255',
            'weight_id' => 'required|exists:weight_classes,id',
            'delivery_method_id' => 'required|exists:delivery_method,id',
            'dimension' => 'required|string|max:255',
            'weight_price' => 'required|numeric|min:0',
            'delivery_price' => 'required|numeric|min:0'
        ];

        if ($deliveryMethod->requires_location) {
            $validationRules['destination_location_id'] = 'required|exists:locations,id';
        } else {
            $validationRules['street'] = 'required|string|max:255';
            $validationRules['house_number'] = 'required|string|max:255';
            $validationRules['cities_id'] = 'required|integer|min:1';
            $validationRules['country_id'] = 'required|integer|min:1';
        }

        $validatedData = $request->validate($validationRules);

        // Verify that the prices match the actual prices from the database
        if ($validatedData['weight_price'] != $weightClass->price || 
            $validatedData['delivery_price'] != $deliveryMethod->price) {
            return back()->withErrors(['price' => 'Invalid price calculation']);
        }

        $validatedData['reference'] = $this->generateUniqueTrackingNumber();
        $validatedData['user_id'] = $userId;
        $validatedData['status'] = 'pending';

        //Mail::to($package->receiverEmail)->send(new PackageCreatedMail($package));

        if (!$deliveryMethod->requires_location) {
            // Create address for the package
            $address = Address::create([
                'street' => $validatedData['street'],
                'house_number' => $validatedData['house_number'],
                'cities_id' => $validatedData['cities_id'],
                'country_id' => $validatedData['country_id']
            ]);

            // Remove address fields from package data
            $packageData = collect($validatedData)
                ->except(['street', 'house_number', 'cities_id', 'country_id'])
                ->toArray();

            // Add the address ID to package data
            $packageData['addresses_id'] = $address->id;

            // Create the package
            $package = Package::create($packageData);
        } else {
            // Create package without address
            $package = Package::create($validatedData);
        }

        if (!$package) {
            return back()->withErrors(['error' => 'Failed to create package']);
        }

        //return redirect()->route('generate-package-label')->with('success', 'Package created successfully');
        return redirect()->route('packages.send-package')->with('success', 'Package created successfully');
    }

    /**
 * Generate a unique tracking number for a package
 * Format: PK + YYYY + MM + XXXXXXXX (where X is random number)
 * Example: PK20250312345678
 *
 * @return string
 * @throws \Exception if unable to generate unique number after maximum attempts
 */
private function generateUniqueTrackingNumber()
{
    $maxAttempts = 100;
    $attempt = 0;
    
    do {
        if ($attempt >= $maxAttempts) {
            throw new \Exception('Unable to generate unique tracking number after ' . $maxAttempts . ' attempts');
        }

        $year = date('Y');
        $month = date('m');
        
        $random = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        
        $trackingNumber = sprintf(
            'PK%s%s%s',
            $year,
            $month,
            $random
        );
        
        $exists = Package::where('reference', $trackingNumber)->exists();
        
        $attempt++;
    } while ($exists);

    return $trackingNumber;
}
    
    public function generateQRcode($packageID){
        $qrCode = QrCode::size(300)->generate($packageID);
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }

    /**
 * Generate a package label PDF
 * 
 * @param int $packageID
 * @return \Illuminate\Http\Response
 * @throws \Illuminate\Auth\Access\AuthorizationException
 */
public function generatePackageLabel($packageID)
{
    if (!Auth::check()) {
        abort(401, 'Unauthorized access');
    }

    $package = Package::with([
        'address.city.country',
        'user.address.city.country'
    ])->findOrFail($packageID);

    if (Auth::user()->id !== $package->user_id) {
        abort(403, 'You are not authorized to access this package label');
    }

    if ($package->deliveryMethod->requires_location) {
        if (!$package->destinationLocation || !$package->destinationLocation->address) {
            abort(404, 'Destination location address not found for this package');
        }
        $receiverAddress = $package->destinationLocation->address;
        $receiverCountry = $package->destinationLocation->address->city->country;
    } else {
        if (!$package->address) {
            abort(404, 'Address not found for this package');
        }
        $receiverAddress = $package->address;
        $receiverCountry = $package->address->city->country;
    }

    // Generate QR code
    $qrCode = base64_encode(QrCode::format('png')
        ->size(150)
        ->margin(0)
        ->generate($packageID));

        $data = [
            'receiver_address' => $receiverAddress,
            'receiver_country' => $receiverCountry,
            'customer' => $package->user,
            'customer_address' => $package->user->address,
            'customer_country' => $package->user->address->city->country,
            'package' => $package,
            'tracking_number' => $package->reference ?? '1Z 999 999 99 9999 999 9',
            'qr_code' => $qrCode
        ];

    $pdf = Pdf::loadView('packages.generate-package-label', $data)->setPaper('a4', 'landscape');
    return $pdf->stream('package-label.pdf');
}
public function packagePayment($packageID) {
    $package = Package::with([
        'user'
    ])
    ->where('user_id', Auth::user()->id)
    ->where('id', $packageID)
    ->first();
    return view('packagepayment',compact('package')); 
}

}


