<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\contract;

class contractController extends Controller
{
    public function contractindex(){
        $contracts = contract::all();
        return view('contract',['contracts'=>$contracts]);
    }

    public function contractcreate(){
        return view('contractcreate');
    }

    public function store(Request $request){
        $data = $request->validate([
        'airline' => 'required',
        'flight' => 'required|integer',
        'weight' => 'required|numeric',
        'room' => 'required|numeric'
        ]);

        contract::create($data);

        return redirect(route('contract'));
    }
}