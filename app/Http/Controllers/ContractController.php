<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;


class ContractController extends Controller
{
    public function contractindex(){
        $contracts = Contract::all();

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

        return redirect(route('workspace.airport.contract'));
    }
}
