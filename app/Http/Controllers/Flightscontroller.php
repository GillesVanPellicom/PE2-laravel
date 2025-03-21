<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\flight;
use Carbon\Carbon;

class Flightscontroller extends Controller
{
    public function flightindex()
    {
        $today = Carbon::now()->format('l'); 
        $weekNumber = Carbon::now()->weekOfYear;

        $flights = Flight::with(['departureAirport', 'arrivalAirport'])
            ->where('departure_day_of_week', $today)
            ->get();

        $flights = $flights->sortBy(function ($flight) use ($weekNumber) {
            return crc32($flight->id . $weekNumber); 
        });

        foreach ($flights as $flight) {
            $random = mt_rand(1, 100); 

            if ($random <= 80) {
                $flight->status = 'On Time'; // 80% chance
            } elseif ($random <= 95) {
                $flight->status = 'Delayed'; // 15% chance
            } else {
                $flight->status = 'Canceled'; // 5% chance
            }

            // Calculate arrival time for each flight
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
        'status' => 'required|in:Scheduled,Delayed,Canceled'
        ]);

        flight::create($data);

        return redirect(route('airport.flights'));
    }

    public function delayFlight(Request $request, $id)
    {
        $flight = Flight::findOrFail($id);

        $data = $request->validate([
            'delay_minutes' => 'required|integer|min:1',
        ]);

        $flight->departure_time = Carbon::parse($flight->departure_time)->addMinutes($data['delay_minutes']);
        $flight->status = 'Delayed';
        $flight->save();

        return redirect()->back()->with('success', 'Flight delayed successfully.');
    }

    public function cancelFlight($id)
    {
        $flight = Flight::findOrFail($id);
        $flight->status = 'Canceled';
        $flight->save();

        return redirect()->back()->with('success', 'Flight canceled successfully.');
    }
}
