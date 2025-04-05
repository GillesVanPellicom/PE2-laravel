<?php
namespace App\Http\Controllers;
use App\Models\Airport;


class AirportController extends Controller
{
    public function airportindex(){
        $airports = Airport::all();
        return view('airport.airports',['airports'=>$airports]);
    }
}