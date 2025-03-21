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

        $flights = Flight::with(['departureAirport', 'arrivalAirport'])
            ->where('departure_day_of_week', $today)
            ->get();

        foreach ($flights as $flight) {
            $random = mt_rand(1, 100); 

            if ($random <= 80) {
                $flight->status = 'On Time';
            } elseif ($random <= 95) {
                $randomDelay = mt_rand(1, 120);

                $flight->departure_time = Carbon::parse($flight->departure_time)->addMinutes($randomDelay)->format('H:i');
                $flight->status = 'Delayed';
                $flight->delay_minutes = $randomDelay;
            } else {
                $flight->status = 'Canceled';
                $flight->departure_time = null;
                $flight->arrival_time = null;
                continue;
            }
    
            $departureTime = Carbon::parse($flight->departure_time);
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

    public function delayFlight($id)
    {
        $flight = Flight::findOrFail($id);

        $randomDelay = mt_rand(1, 120);

        $flight->departure_time = Carbon::parse($flight->departure_time)->addMinutes($randomDelay);
        $flight->status = 'Delayed';
        $flight->save();

        return redirect()->back()->with('success', "Flight delayed by $randomDelay minutes successfully.");
    }

    public function cancelFlight($id)
    {
        $flight = Flight::findOrFail($id);
        $flight->status = 'Canceled';
        $flight->save();

        return redirect()->back()->with('success', 'Flight canceled successfully.');
    }
}
