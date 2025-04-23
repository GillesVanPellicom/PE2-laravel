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
            ->where('departure_day_of_week', "Friday")
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
        try {
            $packageId = $request->input('packageId');
            $flightId = $request->input('flightId');

            \Log::info("Assigning package to flight", [
                'packageId' => $packageId,
                'flightId' => $flightId,
                'requestPayload' => $request->all()
            ]);

            $package = \App\Models\Package::findOrFail($packageId);
            $flight = Flight::findOrFail($flightId);
            $contract = \App\Models\FlightContract::where('flight_id', $flightId)->first();

            if (!$contract) {
                \Log::error("Flight contract not found", ['flightId' => $flightId]);
                return response()->json(['success' => false, 'message' => 'Flight contract not found.']);
            }

            if (!isset($package->weight) || $package->weight <= 0) {
                \Log::error("Invalid package weight", [
                    'packageId' => $packageId,
                    'weight' => $package->weight
                ]);
                return response()->json(['success' => false, 'message' => 'Invalid package weight.']);
            }

            $remainingCapacity = $contract->max_capacity - $flight->current_weight;

            \Log::info("Remaining capacity check", [
                'remainingCapacity' => $remainingCapacity,
                'packageWeight' => $package->weight
            ]);

            if ($package->weight > $remainingCapacity) {
                \Log::info("Package weight exceeds remaining capacity", [
                    'packageId' => $packageId,
                    'flightId' => $flightId,
                    'remainingCapacity' => $remainingCapacity,
                    'packageWeight' => $package->weight
                ]);
                return response()->json(['success' => false, 'message' => 'Package weight exceeds remaining flight capacity.']);
            }

            $package->assigned_flight = $flightId;
            $package->save();

            \Log::info("Package assigned to flight", [
                'packageId' => $packageId,
                'assignedFlight' => $package->assigned_flight
            ]);

            $flight->current_weight += $package->weight;
            $flight->save();

            \Log::info("Flight weight updated", [
                'flightId' => $flightId,
                'currentWeight' => $flight->current_weight
            ]);

            \Log::info("Package assigned successfully", ['packageId' => $packageId, 'flightId' => $flightId]);
            return response()->json(['success' => true, 'message' => 'Package assigned successfully.']);
        } catch (\Throwable $e) {
            \Log::error('Error assigning package to flight', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'packageId' => $request->input('packageId'),
                'flightId' => $request->input('flightId')
            ]);
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred while assigning the package.']);
        }
    }

    public function airports()
    {
        $nextFlight = Flight::with(['departureAirport', 'arrivalAirport'])->where('isActive', true)
            ->where('departure_time', '>=', now())
            ->orderBy('departure_time', 'asc')
            ->first();

        $packages = \App\Models\Package::where('assigned_flight', $nextFlight->id ?? null)->get();

        return view('airport.airports', ['nextFlight' => $nextFlight, 'packages' => $packages]);
    }
}
