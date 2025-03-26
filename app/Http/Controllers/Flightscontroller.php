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

    public function flights()
    { 
        $flights = Flight::with(['departureAirport', 'arrivalAirport'])
            ->get();

        return view('airport.airlines', ['flights' => $flights]);
    }

    public function updateStatus(Request $request, $id)
    {
        $flight = Flight::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:Scheduled,Delayed,Cancelled',
            'delay_minutes' => 'nullable|integer|min:1'
        ]);

        $flight->status = $data['status'];

        if ($data['status'] === 'Delayed' && isset($data['delay_minutes'])) {
            $flight->delay_minutes = $data['delay_minutes'];
            $flight->departure_time = Carbon::parse($flight->departure_time)->addMinutes($data['delay_minutes']);
        } else {
            $flight->delay_minutes = null;
        }

        if ($data['status'] === 'Cancelled') {
            $flight->departure_time = null;
            $flight->arrival_time = null;
        }

        $flight->save();

        return redirect()->back()->with('success', 'Flight status updated successfully.');
    }
}
