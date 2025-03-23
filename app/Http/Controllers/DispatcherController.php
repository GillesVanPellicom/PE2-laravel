<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RouterNodes;
use App\Models\City;

class DispatcherController extends Controller
{
    public function index()
    {
        // Retrieve users who are employees (user_id exists in employees table)
        $employees = User::whereHas('employee')->get();

        // Fetch all distribution centers (RouterNodes with location_type as 'distribution_center')
        $distributionCenters = RouterNodes::where('location_type', 'distribution_center')->get();

        // Fetch all cities
        $cities = City::all();

        // Pass data to the view
        return view('employees.dispatcher', [
            'employees' => $employees,
            'distributionCenters' => $distributionCenters,
            'cities' => $cities,
        ]);
    }
}
