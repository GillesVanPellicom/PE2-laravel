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
use Illuminate\Support\Facades\Mail;

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
        $deliveryMethod = DeliveryMethod::findOrFail($request->delivery_method_id);
        $weightClass = WeightClass::findOrFail($request->weight_id);

        $validationRules = [
            'reference' => 'string|max:255',
            'user_id' => 'required|exists:users,id',
            'origin_location_id' => 'required|exists:locations,id',
            'destination_location_id' => 'exists:locations,id',
            'address_id' => 'exists:addresses,id',           
            'status' => 'required|string|max:255',
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

        $package = Package::create($validatedData);

        Mail::to($package->receiverEmail)->send(new PackageCreatedMail($package));

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

        return redirect()->route('packages.send-package')->with('success', 'Package created successfully');
    }
    
    public function generateQRcode($packageID){
        $qrCode = QrCode::size(300)->generate($packageID);
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}