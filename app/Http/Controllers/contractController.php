<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\contract;


class ContractController extends Controller
{
    public function contractindex(){
        $contracts = contract::all();
        
        return view('airport.contract', ['contracts' => $contracts]);
    }

    public function contractcreate(){

        return view('contractcreate', compact('flights', 'airlines'));
    }

    public function store(Request $request){

        Contract::create([
            'flight_id' => 'flight_id',
            'airline_id' => 'airline_id',
            'max_capacity' => 'max_capacity',
            'price' => 'price'
        ]);

        return redirect(route('airport.contract'));
    }
}