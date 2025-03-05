<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use App\Models\DeliveryMethod;
use App\Models\WeightClass;
use App\Models\Location;
use App\Models\Parcel;
use App\Models\Addresses;
use Laravel\Pail\ValueObjects\Origin\Console;
use Pnlinh\GoogleDistance\Facades\GoogleDistance;

class ParcelController extends Controller
{
    public function create(): View
    {
        if (!session()->has('parcel_data.step1')) {
            session(['current_step' => 1]);
        }

        $countriesJson = json_decode(file_get_contents(resource_path('data/countries.json')), true);
        $countries = collect($countriesJson)
            ->sortBy('name')
            ->mapWithKeys(function ($country) {
                return [$country['code'] => $country['name']];
            })
            ->toArray();

        $deliveryMethods = DeliveryMethod::where('is_active', true)
            ->get()
            ->map(function ($method) {
                return [
                    'id' => $method->code,
                    'name' => $method->name,
                    'description' => $method->description,
                    'price' => $method->price
                ];
            })
            ->toArray();

        $weightClasses = WeightClass::where('is_active', true)
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'weight_min' => $class->weight_min,
                    'weight_max' => $class->weight_max,
                    'price' => $class->price
                ];
            })
            ->toArray();

        $currentStep = session('current_step', 1);

        return view('parcels.send-parcel', compact('countries', 'deliveryMethods', 'weightClasses', 'currentStep'));
    }

    public function store(Request $request)
    {
        // Return JSON responses for AJAX requests
        if ($request->ajax()) {
            try {
                $currentStep = (int) $request->input('current_step', 1);

                if ($currentStep === 1) {
                    $allowedDeliveryMethods = DeliveryMethod::where('is_active', true)
                        ->pluck('code')
                        ->implode(',');

                    $allowedWeightClasses = WeightClass::where('is_active', true)
                        ->pluck('id')
                        ->implode(',');

                    $validated = $request->validate([
                        'country' => 'required|string|size:2',
                        'delivery_method' => 'required|in:' . $allowedDeliveryMethods,
                        'weight_class' => 'required|in:' . $allowedWeightClasses,
                        'reference' => 'nullable|string|max:255',
                    ]);

                    // Save prices in session
                    $validated['delivery_method_price'] = (float) $request->input('delivery_method_price', 0);
                    $validated['weight_price'] = (float) $request->input('weight_price', 0);
                    $validated['total_price'] = $validated['delivery_method_price'] + $validated['weight_price'];

                    session(['parcel_data.step1' => $validated]);
                    session(['current_step' => 2]);
                    session()->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Step 1 completed successfully',
                        'sessionData' => [
                            'parcel_data' => session('parcel_data'),
                            'current_step' => session('current_step'),
                            'pricing' => [
                                'delivery_price' => session('parcel_data.step1.delivery_method_price'),
                                'weight_price' => session('parcel_data.step1.weight_price'),
                                'total_price' => session('parcel_data.step1.total_price')
                            ]
                        ]
                    ]);
                } elseif ($currentStep === 2) {
                    $step1Data = session('parcel_data.step1');
                    if (!$step1Data) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please complete step 1 first'
                        ], 400);
                    }

                    $rules = [
                        'firstname' => 'required|string|max:255|min:2',
                        'lastname' => 'required|string|max:255|min:2',
                        'company' => 'nullable|string|max:255',
                        'email' => 'required|email|max:255',
                        'phone' => 'required|string|max:20|min:10',
                    ];

                    $deliveryMethod = DeliveryMethod::where('code', $step1Data['delivery_method'])->first();
                    if ($deliveryMethod && $deliveryMethod->requires_location) {
                        $locationCodes = Location::where('location_type', $deliveryMethod->code)
                            ->where('is_active', true)
                            ->pluck('id')
                            ->implode(',');
                        $rules['location_code'] = 'required|string|in:' . $locationCodes;
                    } else {
                        $rules = array_merge($rules, [
                            'street' => 'required|string|max:255|min:5',
                            'postal_code' => 'required|string|max:10|min:4',
                            'city' => 'required|string|max:255|min:2',
                        ]);
                    }

                    $validated = $request->validate($rules);
                    session(['parcel_data.step2' => $validated]);
                    session(['current_step' => 3]);
                    session()->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Step 2 completed successfully',
                        'sessionData' => [
                            'parcel_data' => session('parcel_data'),
                            'current_step' => session('current_step'),
                            'pricing' => [
                                'delivery_price' => session('parcel_data.step1.delivery_method_price'),
                                'weight_price' => session('parcel_data.step1.weight_price'),
                                'total_price' => session('parcel_data.step1.total_price')
                            ]
                        ]
                    ]);
                } elseif ($currentStep === 3) {
                    // Validate sender details
                    $validator = Validator::make($request->all(), [
                        'sender_firstname' => 'required|string|min:2|max:255',
                        'sender_lastname' => 'required|string|min:2|max:255',
                        'sender_street' => 'required|string|min:2|max:255',
                        'sender_number' => 'required|string|max:10',
                        'sender_bus' => 'nullable|string|max:10',
                        'sender_postal_code' => 'required|string|min:4|max:10',
                        'sender_city' => 'required|string|min:2|max:255',
                        'sender_country' => 'required|string|size:2',
                        'sender_email' => 'required|email|max:255',
                        'sender_phone' => 'nullable|string|max:20',
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $validator->errors()
                        ], 422);
                    }

                    try {
                        $validated = $validator->validated();
                        session(['parcel_data.step3' => $validated]);

                        // Get all session data
                        $step1Data = session('parcel_data.step1');
                        $step2Data = session('parcel_data.step2');
                        $step3Data = session('parcel_data.step3');

                        // Create sender address record
                        $senderAddress = Addresses::create([
                            'street' => $step3Data['sender_street'],
                            'number' => $step3Data['sender_number'],
                            'bus' => $step3Data['sender_bus'],
                            'postal_code' => $step3Data['sender_postal_code'],
                            'city' => $step3Data['sender_city'],
                            'country' => $step3Data['sender_country'],
                        ]);

                        // Create receiver address record if delivery method is 'address'
                        $receiverAddressId = null;
                        $deliveryMethod = DeliveryMethod::where('code', $step1Data['delivery_method'])->firstOrFail();
                        if (!$deliveryMethod->requires_location) {
                            $receiverAddress = Addresses::create([
                                'street' => $step2Data['street'],
                                'postal_code' => $step2Data['postal_code'],
                                'city' => $step2Data['city'],
                                'country' => $step1Data['country'],
                            ]);
                            $receiverAddressId = $receiverAddress->id;
                        }

                        // Create parcel record
                        $parcel = Parcel::create([
                            'reference' => $step1Data['reference'] ?? null,
                            'country_code' => $step1Data['country'],
                            'delivery_method_id' => $deliveryMethod->id,
                            'weight_class_id' => $step1Data['weight_class'],
                            'destination_location_id' => $deliveryMethod->requires_location ? $step2Data['location_code'] : null,
                            'delivery_price' => $step1Data['delivery_method_price'],
                            'weight_price' => $step1Data['weight_price'],
                            'total_price' => $step1Data['total_price'],
                            
                            // Receiver details
                            'firstname' => $step2Data['firstname'],
                            'lastname' => $step2Data['lastname'],
                            'company' => $step2Data['company'] ?? null,
                            'email' => $step2Data['email'],
                            'phone' => $step2Data['phone'],
                            'address_id' => $receiverAddressId,
                            
                            // Sender details
                            'sender_firstname' => $step3Data['sender_firstname'],
                            'sender_lastname' => $step3Data['sender_lastname'],
                            'sender_email' => $step3Data['sender_email'],
                            'sender_phone' => $step3Data['sender_phone'],
                            'sender_address_id' => $senderAddress->id,
                        ]);

                        // Clear session and return success response
                        session()->forget(['parcel_data', 'current_step']);
                        session()->save();

                        return response()->json([
                            'success' => true,
                            'message' => 'Your parcel has been successfully registered!',
                            'parcel' => $parcel
                        ]);

                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'An error occurred while processing your request.',
                            'error' => $e->getMessage()
                        ], 500);
                    }
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing your request.'
                ], 500);
            }
        }

        // Non-AJAX requests continue with the existing logic...
        $currentStep = (int) $request->input('current_step', 1);

        if ($currentStep === 1) {
            $allowedDeliveryMethods = DeliveryMethod::where('is_active', true)
                ->pluck('code')
                ->implode(',');

            $allowedWeightClasses = WeightClass::where('is_active', true)
                ->pluck('id')
                ->implode(',');

            $validated = $request->validate([
                'country' => 'required|string|size:2',
                'delivery_method' => 'required|in:' . $allowedDeliveryMethods,
                'weight_class' => 'required|in:' . $allowedWeightClasses,
                'reference' => 'nullable|string|max:255',
            ]);

            // Save prices in session
            $validated['delivery_method_price'] = (float) $request->input('delivery_method_price', 0);
            $validated['weight_price'] = (float) $request->input('weight_price', 0);
            $validated['total_price'] = $validated['delivery_method_price'] + $validated['weight_price'];

            session(['parcel_data.step1' => $validated]);
            session(['current_step' => 2]);
            session()->save();

            return redirect()->route('parcel.create');
        } elseif ($currentStep === 2) {
            // Save step 2 data in session before moving to step 3
            $step1Data = session('parcel_data.step1');
            if (!$step1Data) {
                return redirect()->route('parcel.create')
                    ->with('error', 'Please complete step 1 first');
            }

            $rules = [
                'firstname' => 'required|string|max:255|min:2',
                'lastname' => 'required|string|max:255|min:2',
                'company' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20|min:10',
            ];

            $deliveryMethod = DeliveryMethod::where('code', $step1Data['delivery_method'])->first();
            if ($deliveryMethod && $deliveryMethod->requires_location) {
                $locationCodes = Location::where('location_type', $deliveryMethod->code)
                    ->where('is_active', true)
                    ->pluck('id')
                    ->implode(',');
                $rules['location_code'] = 'required|string|in:' . $locationCodes;
            } else {
                $rules = array_merge($rules, [
                    'street' => 'required|string|max:255|min:5',
                    'postal_code' => 'required|string|max:10|min:4',
                    'city' => 'required|string|max:255|min:2',
                ]);
            }

            $validated = $request->validate($rules);
            session(['parcel_data.step2' => $validated]);
            session(['current_step' => 3]);
            session()->save();

            return redirect()->route('parcel.create');
        } elseif ($currentStep === 3) {
            // Validate sender details
            $validator = Validator::make($request->all(), [
                'sender_firstname' => 'required|string|min:2|max:255',
                'sender_lastname' => 'required|string|min:2|max:255',
                'sender_street' => 'required|string|min:2|max:255',
                'sender_number' => 'required|string|max:10',
                'sender_bus' => 'nullable|string|max:10',
                'sender_postal_code' => 'required|string|min:4|max:10',
                'sender_city' => 'required|string|min:2|max:255',
                'sender_country' => 'required|string|size:2',
                'sender_email' => 'required|email|max:255',
                'sender_phone' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                $validated = $validator->validated();
                session(['parcel_data.step3' => $validated]);

                // Get all session data
                $step1Data = session('parcel_data.step1');
                $step2Data = session('parcel_data.step2');
                $step3Data = session('parcel_data.step3');

                // Create sender address record
                $senderAddress = Addresses::create([
                    'street' => $step3Data['sender_street'],
                    'number' => $step3Data['sender_number'],
                    'bus' => $step3Data['sender_bus'],
                    'postal_code' => $step3Data['sender_postal_code'],
                    'city' => $step3Data['sender_city'],
                    'country' => $step3Data['sender_country'],
                ]);

                // Create receiver address record if delivery method is 'address'
                $receiverAddressId = null;
                $deliveryMethod = DeliveryMethod::where('code', $step1Data['delivery_method'])->firstOrFail();
                if (!$deliveryMethod->requires_location) {
                    $receiverAddress = Addresses::create([
                        'street' => $step2Data['street'],
                        'postal_code' => $step2Data['postal_code'],
                        'city' => $step2Data['city'],
                        'country' => $step1Data['country'],
                    ]);
                    $receiverAddressId = $receiverAddress->id;
                }

                // Create parcel record
                $parcel = Parcel::create([
                    'reference' => $step1Data['reference'] ?? null,
                    'country_code' => $step1Data['country'],
                    'delivery_method_id' => $deliveryMethod->id,
                    'weight_class_id' => $step1Data['weight_class'],
                    'destination_location_id' => $deliveryMethod->requires_location ? $step2Data['location_code'] : null,
                    'delivery_price' => $step1Data['delivery_method_price'],
                    'weight_price' => $step1Data['weight_price'],
                    'total_price' => $step1Data['total_price'],
                    
                    // Receiver details
                    'firstname' => $step2Data['firstname'],
                    'lastname' => $step2Data['lastname'],
                    'company' => $step2Data['company'] ?? null,
                    'email' => $step2Data['email'],
                    'phone' => $step2Data['phone'],
                    'address_id' => $receiverAddressId,
                    
                    // Sender details
                    'sender_firstname' => $step3Data['sender_firstname'],
                    'sender_lastname' => $step3Data['sender_lastname'],
                    'sender_email' => $step3Data['sender_email'],
                    'sender_phone' => $step3Data['sender_phone'],
                    'sender_address_id' => $senderAddress->id,
                ]);

                // Clear session and return success response
                session()->forget(['parcel_data', 'current_step']);
                session()->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Your parcel has been successfully registered!',
                    'parcel' => $parcel
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing your request.',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }
} 