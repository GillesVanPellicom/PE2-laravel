<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flight;
use Carbon\Carbon;

class Flightscontroller extends Controller
{
    public function flightindex()
    {
        $today = Carbon::now()->format('l'); 

        $flights = Flight::with(['departureAirport', 'arrivalAirport'])
            ->where('departure_day_of_week', "Friday")
            ->get();

        foreach ($flights as $flight) {
            $departureTime = Carbon::parse($flight->departure_time);
            if($flight->status == 'Delayed'){
                $departureTime->addMinutes($flight->delayed_minutes);
            }
            $flightDuration = $flight->time_flight_minutes;

            $arrivalTime = $departureTime->addMinutes($flightDuration);

            $flight->arrival_time = $arrivalTime;
            if($flight->status == 'Canceled'){
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
        $flights = Flight::with(['departureAirport', 'arrivalAirport'])
            ->get();

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

    public function flightPackages()
    {
        $employeeLocationId = 1; // Replace with logic to get the employee's location dynamically if needed

        $flights = Flight::with([
            'departureAirport', 
            'arrivalAirport', 
            'arrivalLocation.packages', 
            'departureLocation.packages'
        ])->get();

        $packages = \App\Models\Package::where('current_location_id', $employeeLocationId)
            ->where('status', 'Pending') // Filter packages by status "Pending"
            ->get();

        return view('airport.flightpackages', compact('flights', 'packages'));
    }

    public function assignFlight(Request $request)
    {
        $packageId = $request->input('packageId');
        $flightId = $request->input('flightId');

        $package = \App\Models\Package::findOrFail($packageId);
        $flight = Flight::findOrFail($flightId);
        $contract = \App\Models\FlightContract::where('flight_id', $flightId)->first();

        if (!$contract) {
            return response()->json(['success' => false, 'message' => 'Flight contract not found.']);
        }

        // Ensure the package has a valid weight
        if (!isset($package->weight) || $package->weight <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid package weight.']);
        }

        $remainingCapacity = $contract->max_capacity - $flight->current_weight;

        // Check if the package weight exceeds the remaining capacity
        if ($package->weight > $remainingCapacity) {
            return response()->json(['success' => false, 'message' => 'Package weight exceeds remaining flight capacity.']);
        }

        try {
            // Assign the package to the flight
            $package->assigned_flight = $flightId;
            $package->save();

            // Update the flight's current weight
            $flight->current_weight += $package->weight;
            $flight->save();

            return response()->json(['success' => true, 'message' => 'Package assigned successfully.']);
        } catch (\Throwable $e) {
            // Log the error for debugging purposes
            \Log::error('Error assigning package to flight: ' . $e->getMessage());

            // Return a generic error message only for unexpected exceptions
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred while assigning the package.']);
        }
    }
}
