<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{Employee, Country, City, Address};
use App\Rules\Validate_Adult;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        //https://laravel.com/docs/11.x/views#passing-data-to-views
        $employees = Employee::with('address.city.country')->get();
        return view('employees.index', compact('employees'));
    }

    public function managerCalendar()
    {
        $employees = Employee::all();
        return view('employees.manager_calendar', compact('employees')); 
    }

    public function create()
    {
        return view('employees.create', ['countries' => Country::all()], ['cities' => City::all()]);
    }

    public function store_employee(Request $request)
    {

        //https://stackoverflow.com/questions/47211686/list-of-laravel-validation-rules
        //https://www.youtube.com/watch?v=q9PeXmrQLpI



        /*
            making a custom validation
            php artisan make:rule Validate_Adult    -> creates a new rule in App\Rules
        */

        $request->validate([
            'street' => 'required',
            'house_number' => 'required',
            'city' => 'required|integer|exists:cities,id',
        ],
        [
            'street.required' => 'Street is required',
            'house_number.required' => 'House number is required',
            'city.required' => 'City is required',
            'city.exists' => 'Please select a city',
        ]);

        $address = [
            'street' => $request->street,
            'house_number' => $request->house_number,
            'cities_id' => $request->city,
            'country_id' => City::find($request->city)->country_id,
        ];

        $request->validate([
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email', // Unique email check
            'phone' => 'required|string|max:15', // Adjust the max length as needed
            'birth_date' => 'required|date|before:today', // Ensure the birth date is before today
            'nationality' => 'required|string|max:255',
            'leave_balance' => 'required|integer|min:0', // Make sure the leave balance is a positive number
        ], [
            'lastname.required' => 'Last name is required.',
            'firstname.required' => 'First name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'phone.required' => 'Phone number is required.',
            'birth_date.required' => 'Birth date is required.',
            'birth_date.before' => 'Birth date must be a date before today.',
            'nationality.required' => 'Nationality is required.',
            'leave_balance.required' => 'Leave balance is required.',
            'leave_balance.integer' => 'Leave balance must be a valid number.',
            'leave_balance.min' => 'Leave balance must be at least 0.',
        ]);

        $existingAddress = Address::where('street', $request->street)->where('house_number', $request->house_number)->where('cities_id', $request->city)->first();

        if ($existingAddress) 
        {
            $new_address_id = $existingAddress->id;
        } else 
        {
            $new_address = Address::create($address);
            $new_address_id = $new_address->id;
        }

        $employee = [
            'last_name' => $request->lastname,
            'first_name' => $request->firstname,
            'email' => $request->email,
            'phone_number' => $request->phone,
            'birth_date' => $request->birth_date,
            'address_id' => $new_address_id,
            'nationality' => $request->nationality,
            'leave_balance' => $request->leave_balance,
            'city_id' => $request->city,
        ];
        dump($address);
        dump($employee);

        Employee::create($employee);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully');;
    }
}
