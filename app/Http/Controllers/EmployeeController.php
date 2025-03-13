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
        $employees = Employee::with('address.city.country')->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create', ['countries' => Country::all()], ['cities' => City::all()]);
    }

    public function store_employee(Request $request)
    {

        $request->validate([
            'street' => 'required',
            'house_number' => 'required|integer|min:0',
            'bus_number' => 'nullable|alpha',
            'city' => 'required|integer',

            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|unique:employees,phone_number|regex:/^([0-9\s-+()]*)$/|min:10',
            'birth_date' => ['required', 'date', new Validate_Adult('employee')],
            'nationality' => 'required|string|max:255',
        ],
        [
            'street.required' => 'Street is required',
            'house_number.required' => 'House number is required',
            'house_number.integer' => 'House number must be a number',
            'house_number.min' => 'House number must be larger than 0',
            'city.required' => 'City is required',

            'lastname.required' => 'Lastname is required.',
            'lastname.max' => 'Lastname is too long.',
            'firstname.required' => 'Firstname is required.',
            'firstname.max' => 'Firstname is too long.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'Phone number is already in use.',
            'phone.regex' => 'Phone number is not in a correct format.',
            'phone.min' => 'Phone number is too short.',
            'birth_date.required' => 'Birth date is required.',
            'nationality.required' => 'Nationality is required.',
            'nationality.max' => 'Nationality is too long.',
        ]);

        $address = [
            'street' => $request->street,
            'house_number' => $request->house_number,
            'bus_number' => strtoupper($request->bus_number),
            'cities_id' => $request->city,
        ];

        $existingAddress = Address::where('street', $request->street)->where('house_number', $request->house_number)->where('cities_id', $request->city)->where(strtoupper('bus_number'), strtoupper($request->bus_number))->first();

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
            'leave_balance' => 0,
        ];

        Employee::create($employee);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully');;
    }
}
