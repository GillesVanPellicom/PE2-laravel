<?php
namespace App\Http\Controllers;
use App\Models\airport;


class AirportController extends Controller
{
    public function airportindex(){
        $airports = airport::all();
        return view('airport.airports',['airports'=>$airports]);
    }
}