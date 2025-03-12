<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\flight;

class flightscontroller extends Controller
{
    public function flightindex(){
        $flights = flight::all();
        return view('flights',['flights'=>$flights]);
    }

    public function flightcreate(){
        return view('flightcreate');
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
