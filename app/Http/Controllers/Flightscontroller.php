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
            'status' => 'required|in:On time,Delayed,Cancelled',
            'delayed_minutes' => 'nullable|integer|min:1'
        ]);

        $flight = Flight::findOrFail($id);

        if ($data['status'] === 'Delayed') {
            $flight->status = 'Delayed';
            $flight->delayed_minutes = (int) ($data['delayed_minutes'] ?? 0);
        } elseif ($data['status'] === 'Cancelled') {
            $flight->status = 'Cancelled';
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
        $flights = Flight::with(['departureAirport', 'arrivalAirport', 'arrivalLocation.packages', 'departureLocation.packages'])->get();
        return view('airport.flightpackages', compact('flights'));
    }
}
