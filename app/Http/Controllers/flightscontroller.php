<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\flight;
use Carbon\Carbon;

class flightscontroller extends Controller
{
    public function flightindex()
{
    $today = Carbon::now()->format('l'); // Get today's day name (e.g., "Monday")

    // Get flights where 'departure_day_of_week' matches today
    $flights = Flight::with(['departureAirport', 'arrivalAirport'])
        ->where('departure_day_of_week', $today)
        ->get();

    // Calculate arrival time for each flight
    foreach ($flights as $flight) {
        // Assuming you have 'departure_time' and 'time_flight_minutes'
        $departureTime = Carbon::parse($flight->departure_time); // Parse departure time
        $flightDuration = $flight->time_flight_minutes; // Time in minutes

        // Calculate arrival time
        $arrivalTime = $departureTime->addMinutes($flightDuration);

        // Store the arrival time in the flight object for later use in view
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
        'status' => 'required'
        ]);

        flight::create($data);

        return redirect(route('airport.flights'));
    }
}
