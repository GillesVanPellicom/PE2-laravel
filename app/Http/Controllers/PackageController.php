<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\WeightClass;
use App\Models\DeliveryMethod;
use App\Models\Location;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\PackageCreatedMail;
use App\Models\Country;
use App\Models\City;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class PackageController extends Controller
{

    public function index()
    {
        if (request()->has('search')) {
            $packages = Package::where('reference', 'like', '%' . request('search','') . '%')
                ->orWhere('name', 'like', '%' . request('search','') . '%')
                ->orWhere('receiverEmail', 'like', '%' . request('search','') . '%')
                ->orWhere('receiver_phone_number', 'like', '%' . request('search','') . '%')
                ->paginate(10)->withQueryString();
        } else {
            $packages = Package::paginate(10)->withQueryString();
        }
        return view('pickup.dashboard', compact('packages'));
    }
    public function show ($id) {
        try {
            $id = $id !== ' ' ? $id : request()->get('id');
            $package = Package::where('id', $id)->orWhere('reference', $id)->firstOrFail();
            return view('pickup.packageInfo', compact('package'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            //return response()->view('errors.pickup.404', ['message' => 'The entered package not found'], 404);
            return redirect()->route('pickup.dashboard')->with('package-not-found', 'The package "'.$id.'" does not exist');
        } catch (\Exception $e) {
            return redirect()->route('pickup.dashboard')->with('error' , 'An unexpected error occurred, retry again or contact your administrator');
        }
    }
    public function setStatusPackage ($id) {
        $package = Package::findOrFail($id);
        $statusToSet = request()->get('status')?? '';
        $package->update(['status' => $statusToSet]);
        return redirect()->route('pickup.dashboard')->with('success', 'The state of the package: '.$package->reference.' was successfully updated to '.$statusToSet);
    }
    public function showPackagesToReturn () {
        $packagesThatNeedToBeReturned = Package::where('status', '!=', 'Delivered')
            ->where('status', '!=', 'Returned')
            ->where('status', '!=', 'Cancelled')
            ->where('updated_at', '<', Carbon::now()->subDays(7))
            ->paginate(10);
        return view('pickup.packages-to-return', compact('packagesThatNeedToBeReturned'));
    }
    public function showReceivingPackages () {
        $packages = Package::where('status', '!=', 'delivered')->paginate(10);
        return view('pickup.receiving-packages', compact('packages'));
    }

    public function mypackages()
    {
        $packages = Package::with([
            'user',
            'deliveryMethod',
            'destinationLocation.address.city.country',
            'address.city.country'
        ])
        ->where('user_id', Auth::user()->id)
        ->get();

        foreach ($packages as $package) {
            if ($package->deliveryMethod->requires_location) {
                if (!$package->destinationLocation || !$package->destinationLocation->address) {
                    abort(404, 'Destination location address not found for this package');
                }
            } else {
                if (!$package->address) {
                    abort(404, 'Address not found for this package');
                }
            }
        }

        $receiving_packages = Package::with([
            'user',
            'deliveryMethod',
            'destinationLocation.address.city.country',
            'address.city.country'
        ])
        ->where('receiverEmail', Auth::user()->email)
        ->where('user_id', '!=', Auth::user()->id)
        ->where('paid', true)
        ->get();

        foreach ($receiving_packages as $package) {
            if ($package->deliveryMethod->requires_location) {
                if (!$package->destinationLocation || !$package->destinationLocation->address) {
                    abort(404, 'Destination location address not found for this package');
                }
            } else {
                if (!$package->address) {
                    abort(404, 'Address not found for this package');
                }
            }
        }

        return view('Packages.my-packages', [
            'packages' => $packages,
            'receiving_packages' => $receiving_packages
        ]);
    }

    public function packagedetails($packageID)
    {
        $package = Package::with([
            'user',
            'deliveryMethod',
            'destinationLocation.address.city.country',
            'address.city.country'
        ])
        ->where('id', $packageID)
        ->first();

        if (!$package) {
            abort(404, 'Package not found');
        }

        if (Auth::user()->id !== $package->user_id && Auth::user()->email !== $package->receiverEmail) {
            abort(403, 'You are not authorized to access this package label');
        }

        // Get origin and destination addresses
        $originAddress = $package->user->address->city->country->country_name;
        $destinationAddress = $package->deliveryMethod->requires_location
            ? $package->destinationLocation->address->city->country->country_name
            : $package->address->city->country->country_name;

        // Calculate estimated delivery

            $deliveryEstimate = $this->calculateEstimatedDelivery($originAddress, $destinationAddress);
            $package->delivery_estimate = $deliveryEstimate;


        $qrCode = base64_encode(QrCode::format('png')
            ->size(150)
            ->margin(0)
            ->generate($packageID));

        return view('Packages.package-details', [
            'package' => $package,
            'qrCode' => $qrCode
        ]);
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
        $userAddress = Auth::user()->address;

        // check if a Location exists with the user's address_id
        $originLocation = Location::where('addresses_id', $userAddress->id)->first();

        if (!$originLocation) {
            try {
                $addressString = urlencode(
                    $userAddress->street . ' ' .
                    $userAddress->house_number . ', ' .
                    $userAddress->city->name . ', ' .
                    $userAddress->city->country->country_name
                );

                // Make API request to Geoapify
                $response = Http::get('https://api.geoapify.com/v1/geocode/search', [
                    'text' => $addressString,
                    'apiKey' => env('GEOAPIFY_API_KEY'),
                    'format' => 'json',
                    'limit' => 1
                ]);


                $geocodeData = $response->json();

                // If the response is not successful
                if (!$response->successful()) {
                    return back()->withErrors(['error' => 'Geocoding service error: ' . $response->status()]);
                }

                // If no results found
                if (empty($geocodeData['results'])) {

                    $alternativeAddress = urlencode(
                        $userAddress->street . ' ' .
                        $userAddress->house_number . ' ' .
                        $userAddress->city->name . ' ' .
                        $userAddress->city->country->country_name
                    );


                    $response = Http::get('https://api.geoapify.com/v1/geocode/search', [
                        'text' => $alternativeAddress,
                        'apiKey' => env('GEOAPIFY_API_KEY'),
                        'format' => 'json',
                        'limit' => 1
                    ]);

                    $geocodeData = $response->json();

                    if (empty($geocodeData['results'])) {
                        return back()->withErrors(['error' => 'Address could not be found']);
                    }
                }

                $location = $geocodeData['results'][0];


                $originLocation = Location::create([
                    'addresses_id' => $userAddress->id,
                    'location_type' => 'ADDRESS',
                    'description' => 'Customer Address'. ' ' . Auth::user()->first_name . ' ' . Auth::user()->last_name,
                    'contact_number' => Auth::user()->phone_number,
                    'latitude' => $location['lat'],
                    'longitude' => $location['lon'],
                    'is_active' => true
                ]);

            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Error processing address location: ' . $e->getMessage()]);
            }
        }

        $deliveryMethod = DeliveryMethod::findOrFail($request->delivery_method_id);
        $weightClass = WeightClass::findOrFail($request->weight_id);

        $validationRules = [
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
            $validationRules['addressInput'] = 'required|string|max:255';
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
        $validatedData['origin_location_id'] = $originLocation->id;

        if (!$deliveryMethod->requires_location) {
            try {
                // Get address details from Geoapify
                $response = Http::get('https://api.geoapify.com/v1/geocode/search', [
                    'text' => $validatedData['addressInput'],
                    'apiKey' => env('GEOAPIFY_API_KEY'),
                    'format' => 'json',
                    'limit' => 1
                ]);

                if (!$response->successful() || empty($response->json()['results'])) {
                    return back()->withErrors(['error' => 'Could not validate address']);
                }

                $addressData = $response->json()['results'][0];

                // Extract address components
                $street = $addressData['street'] ?? '';
                $houseNumber = $addressData['housenumber'] ?? '';
                $busNumber = isset($addressData['unit']) ? $addressData['unit'] : null;
                $city = $addressData['city'] ?? '';
                $postalCode = $addressData['postcode'] ?? '';
                $countryName = $addressData['country'] ?? '';

                // Check if country exists
                $country = Country::firstOrCreate(
                    ['country_name' => $countryName]
                );

                // Check if city exists
                $city = City::firstOrCreate(
                    [
                        'name' => $city,
                        'postcode' => $postalCode,
                        'country_id' => $country->id
                    ]
                );

                // Check if exact address exists
                $existingAddress = Address::where([
                    'street' => $street,
                    'house_number' => $houseNumber,
                    'bus_number' => $busNumber,
                    'cities_id' => $city->id
                ])->first();

                if ($existingAddress) {
                    $address = $existingAddress;
                    // Check if location exists for this address
                    $destinationLocation = Location::where('addresses_id', $address->id)
                        ->where('location_type', 'ADDRESS')
                        ->first();
                } else {
                    // Create new address
                    $address = Address::create([
                        'street' => $street,
                        'house_number' => $houseNumber,
                        'bus_number' => $busNumber,
                        'cities_id' => $city->id
                    ]);
                    $destinationLocation = null;
                }

                // Create destination location only if it doesn't exist
                if (!$destinationLocation) {
                    $destinationLocation = Location::create([
                        'addresses_id' => $address->id,
                        'location_type' => 'ADDRESS',
                        'description' => 'Customer Address ' . $validatedData['name'] . ' ' . $validatedData['lastName'],
                        'contact_number' => $validatedData['receiver_phone_number'],
                        'latitude' => $addressData['lat'],
                        'longitude' => $addressData['lon'],
                        'is_active' => true
                    ]);
                }

                // Prepare package data
                $packageData = collect($validatedData)
                    ->except(['addressInput'])
                    ->merge([
                        'reference' => $this->generateUniqueTrackingNumber(),
                        'user_id' => $userId,
                        'status' => 'Pending',
                        'origin_location_id' => $originLocation->id,
                        'destination_location_id' => $destinationLocation->id,
                        'addresses_id' => $address->id
                    ])
                    ->toArray();

                // Create the package
                $package = Package::create($packageData);

            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Error processing address: ' . $e->getMessage()]);
            }
        } else {
            $destinationLocation = Location::findOrFail($validatedData['destination_location_id']);
            $packageData = collect($validatedData)
                ->merge([
                    'reference' => $this->generateUniqueTrackingNumber(),
                    'user_id' => $userId,
                    'status' => 'pending',
                    'origin_location_id' => $originLocation->id,
                    'destination_location_id' => $destinationLocation->id,
                    'addresses_id' => $destinationLocation->addresses_id  // Add this line
                ])
                ->toArray();

            $package = Package::create($packageData);
        }

        $package->getMovements();

        if (!$package) {
            return back()->withErrors(['error' => 'Failed to create package']);
        }

        return redirect()->route('packagepayment')->with('success', 'Package created successfully');
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

    /**
     * Generate estimated delivery date and time based on origin and destination addresses
     *
     * @param string $originAddress
     * @param string $destinationAddress
     * @return array
     */
    public function calculateEstimatedDelivery($originAddress, $destinationAddress)
    {
        $apiKey = 'AIzaSyCCrnahO6OrXWuQ_BiNcTN6TtiZvqqBOzU';

        // Format addresses for API
        $origin = urlencode($originAddress);
        $destination = urlencode($destinationAddress);

        // Make request to Google Distance Matrix API
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $origin,
            'destinations' => $destination,
            'units' => 'metric',
            'key' => $apiKey
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch distance from Google API');
        }

        $data = $response->json();

        // Check if we got a valid response
        if ($data['status'] !== 'OK' || empty($data['rows'][0]['elements'][0]['distance'])) {
            throw new \Exception('Invalid response from Distance Matrix API');
        }

        // Get distance in kilometers
        $distance = $data['rows'][0]['elements'][0]['distance']['value'] / 1000; // Convert meters to kilometers

        // Calculate delivery days based on distance
        if ($distance < 50) {
            $deliveryDays = 1; // Same city
        } else {
            $deliveryDays = match(true) {
                $distance < 300 => rand(1, 2),      // Nearby cities
                $distance < 1000 => rand(2, 3),     // Same region
                $distance < 3000 => rand(3, 5),     // Same country/neighboring countries
                $distance < 8000 => rand(5, 7),     // Continental
                default => rand(7, 10)              // Intercontinental
            };
        }

        // Add processing time and calculate final date
        $processingDays = 1;
        $totalDays = $deliveryDays + $processingDays;
        $estimatedDate = Carbon::now()->addDays($totalDays);

        // Adjust for weekends
        while ($estimatedDate->isWeekend()) {
            $estimatedDate->addDay();
        }

        // Generate delivery time window (between 9 AM and 5 PM)
        $startHour = rand(9, 13); // Start time between 9 AM and 1 PM
        $startTime = Carbon::parse($estimatedDate)->setHour($startHour)->setMinute(0);
        $endTime = (clone $startTime)->addHours(rand(4, 6)); // Add 4-6 hours for delivery window

        // If end time goes beyond 5 PM, adjust both start and end times
        if ($endTime->hour > 17) {
            $endTime = Carbon::parse($estimatedDate)->setHour(17)->setMinute(0); // Set to 5 PM
            $startTime = (clone $endTime)->subHours(rand(4, 6)); // Work backwards from 5 PM
        }

        return [
            'estimated_date' => $estimatedDate->format('Y-m-d'),
            'delivery_window' => [
                'start' => $startTime->format('H:i'),
                'end' => $endTime->format('H:i'),
            ],
            'distance_km' => round($distance, 2)
        ];
    }
}
