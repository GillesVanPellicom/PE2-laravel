<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flight;
use App\Models\FlightContract;
use Carbon\Carbon;

class Flightscontroller extends Controller
{
    public function flightindex()
    {
        $today = Carbon::now()->format('l'); 

        $flights = Flight::with(['departureAirport', 'arrivalAirport', 'contract'])
            ->whereHas('contract', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                });
            })
            ->where('departure_day_of_week', 'Friday')
            ->get();

        foreach ($flights as $flight) {
            $departureTime = Carbon::parse($flight->departure_time);
            if ($flight->status == 'Delayed') {
                $departureTime->addMinutes($flight->delayed_minutes);
            }
            $flightDuration = $flight->time_flight_minutes;

            $arrivalTime = $departureTime->addMinutes($flightDuration);

            $flight->arrival_time = $arrivalTime;
            if ($flight->status == 'Canceled') {
                $flight->arrival_time = "/";
            }
        }

        return view('airport.flights', ['flights' => $flights]);
        
    }

    public function flightcreate(){
        return view('airport.flightcreate');
    }

    public function store(Request $request){
        $data = $request->validate([
        'airplane_id'=> 'required',
        'departure_time' => 'required',
        'arrival_time' => 'required',
        'depart_location_id' => 'required',
        'arrive_location_id' => 'required',
        'status' => 'required|in:Scheduled,Delayed,Canceled'
        ]);

        flight::create($data);

        return redirect(route('airport.flights'));
    }

    public function flights()
    { 
        $flights = Flight::with(['departureAirport', 'arrivalAirport', 'contract'])->get();

        foreach ($flights as $flight) {
            if ($flight->contract && $flight->contract->isExpired()) {
                $flight->isActive = false;
                $flight->save();
            }
        }

        return view('airport.airlines', ['flights' => $flights]);
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:On time,Delayed,Canceled',
            'delayed_minutes' => 'nullable|integer|min:1'
        ]);

        $flight = Flight::findOrFail($id);

        if ($data['status'] === 'Delayed') {
            $flight->status = 'Delayed';
            $flight->delayed_minutes = (int) ($data['delayed_minutes'] ?? 0);
        } elseif ($data['status'] === 'Canceled') {
            $flight->status = 'Canceled';
            $flight->delayed_minutes = null;
        } else {
            $flight->status = 'On time';
            $flight->delayed_minutes = null;
        }

        $flight->save();

        return redirect()->back()->with('success', 'Flight status updated successfully.');
    }

    public function updateContractEndDate(Request $request, $id)
    {
        $data = $request->validate([
            'end_date' => 'required|date|after_or_equal:today',
        ]);

        $contract = FlightContract::where('flight_id', $id)->firstOrFail();
        $contract->end_date = $data['end_date'];
        $contract->save();

        return redirect()->back()->with('success', 'End date updated successfully.');
    }

    public function flightPackages()
    {
        $employeeLocationId = '@AIR_EBBR'; // Replace with logic to get the employee's location dynamically if needed

        $flights = Flight::with([
            'departureAirport', 
            'arrivalAirport', 
            'arrivalLocation.packages', 
            'departureLocation.packages'
        ])->get();

        $packages = \App\Models\Package::where('current_location_id', $employeeLocationId)
            ->where('status', 'Pending') // Filter packages by status "Pending"
            ->get();

        $routerNodes = \App\Models\RouterNodes::all(); // Fetch all RouterNodes

        return view('airport.flightpackages', compact('flights', 'packages', 'routerNodes'));
    }

    public function assignFlight(Request $request)
    {
        try {
            $packageIds = $request->input('packageIds', []);
            $flightId = $request->input('flightId');

            if (empty($packageIds)) {
                return response()->json(['success' => false, 'message' => 'No packages selected for assignment.']);
            }

            $flight = Flight::findOrFail($flightId);
            $contract = FlightContract::where('flight_id', $flightId)->first();

            if (!$contract) {
                return response()->json(['success' => false, 'message' => 'Flight contract not found.']);
            }

            $remainingCapacity = $contract->max_capacity - $flight->current_weight;

            foreach ($packageIds as $packageId) {
                $package = \App\Models\Package::findOrFail($packageId);

                if (!isset($package->weight) || $package->weight <= 0) {
                    return response()->json(['success' => false, 'message' => "Invalid weight for package ID: $packageId."]);
                }

                // If the package is already assigned to a flight, subtract its weight from the current flight's weight
                if ($package->assigned_flight) {
                    $previousFlight = Flight::find($package->assigned_flight);
                    if ($previousFlight) {
                        $previousFlight->current_weight -= $package->weight;
                        $previousFlight->save();
                    }
                }

                if ($package->weight > $remainingCapacity) {
                    return response()->json(['success' => false, 'message' => 'Package weight exceeds remaining flight capacity.']);
                }

                $package->assigned_flight = $flightId;
                $package->save();

                $remainingCapacity -= $package->weight;
            }

            $flight->current_weight = $contract->max_capacity - $remainingCapacity;
            $flight->save();

            return response()->json(['success' => true, 'message' => 'Packages assigned successfully.']);
        } catch (\Throwable $e) {
            \Log::error('Error assigning packages to flight', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'packageIds' => $request->input('packageIds'),
                'flightId' => $request->input('flightId')
            ]);
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred while assigning the packages.']);
        }
    }

    public function airports()
    {
        // Dynamically get the current airport ID from the employee's contract
        $currentAirportId = auth()->user()->employee->contracts->location_id;

        $flights = Flight::with(['departureAirport', 'arrivalAirport'])->whereIn('status', ['Delayed', 'Canceled'])->get();
        $messages = [];

        foreach ($flights as $flight) {
            if ($flight->status === 'Delayed') {
                if ($flight->depart_location_id == $currentAirportId) {
                    $direction = 'to';
                    $location = $flight->arrivalAirport->name ?? 'unknown location';
                } elseif ($flight->arrive_location_id == $currentAirportId) {
                    $direction = 'from';
                    $location = $flight->departureAirport->name ?? 'unknown location';
                } else {
                    continue; // Skip flights not related to the current airport
                }
                $messages[] = "Flight {$flight->id} {$direction} {$location} is delayed, make sure the packages that need rerouting are rerouted.";
            } elseif ($flight->status === 'Canceled') {
                if ($flight->depart_location_id == $currentAirportId) {
                    $direction = 'to';
                    $location = $flight->arrivalAirport->name ?? 'unknown location';
                } elseif ($flight->arrive_location_id == $currentAirportId) {
                    $direction = 'from';
                    $location = $flight->departureAirport->name ?? 'unknown location';
                } else {
                    continue; // Skip flights not related to the current airport
                }
                $messages[] = "Flight {$flight->id} {$direction} {$location} is cancelled, make sure the packages are rerouted.";
            }
        }

        $today = Carbon::now()->format('l'); // Get today's day of the week

        $nextFlight = Flight::with(['departureAirport', 'arrivalAirport'])
            ->where('isActive', true)
            ->where('departure_time', '>=', now())
            ->where('depart_location_id', $currentAirportId) // Ensure it's an outgoing flight
            ->where('departure_day_of_week', $today) // Ensure the departure day matches today's day
            ->orderBy('departure_time', 'asc')
            ->first();

        $packages = \App\Models\Package::where('assigned_flight', $nextFlight->id ?? null)->get();

        return view('airport.airports', compact('messages'), ['nextFlight' => $nextFlight, 'packages' => $packages]);
    }
}
