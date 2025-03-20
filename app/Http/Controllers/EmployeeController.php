<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{Employee, Country, City, Address, EmployeeContract, User, EmployeeFunction, Team};
use Illuminate\Support\Facades\Hash;
use App\Rules\Validate_Adult;
use Illuminate\Http\Request;
use carbon\Carbon;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::whereHas('employee', function ($query) {
            $query->whereHas('contracts', function ($subQuery) {
                $subQuery->where('end_date', '>', Carbon::now())->orWhereNull('end_date');
            });
        })->get();
        return view('employees.index', compact('employees'));
    }

    public function managerCalendar()
    {
        $employees = Employee::all();
        return view('employees.manager_calendar', compact('employees')); 
    }

    public function create()
    {
        return view('employees.create', ['countries' => Country::all(), 'cities' => City::all(), 'teams' => Team::all()]);
    }

    public function store_employee(Request $request)
    {

        $request->validate([
            'street' => 'required',
            'house_number' => 'required|integer|min:0',
            'bus_number' => 'nullable|alpha',
            'city' => 'required|integer|min:1',

            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone_number|regex:/^\+?[0-9\s\-()]{10,}$/',
            'birth_date' => ['required', 'date', new Validate_Adult('employee')],
            'team' => 'required|integer|min:1',
        ],
        [
            'street.required' => 'Street is required',
            'house_number.required' => 'House number is required',
            'house_number.integer' => 'House number must be a number',
            'house_number.min' => 'House number must be larger than 0',
            'city.required' => 'City is required',
            'city.min' => 'Please enter a city',

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
            'team.required' => 'Team is required.',
            'team.min' => 'Please enter a team',
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

        $user = [
            'last_name' => $request->lastname,
            'first_name' => $request->firstname,
            'email' => $request->email,
            'phone_number' => $request->phone,
            'birth_date' => $request->birth_date,
            'address_id' => $new_address_id,
            'password' => Hash::make('password123')
        ];

        $new_user = User::create($user);
        $user_id = $new_user->id;

        $employee = [
            'leave_balance' => 0,
            'user_id' => $user_id,
            'team_id' => $request->team,
        ];

        Employee::create($employee);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully');;
    }

    public function contracts()
    {
        $contracts = EmployeeContract::where('end_date', '>', Carbon::now())->orWhereNull('end_date')->get();
        $contracts = EmployeeContract::all();
        return view('employees.contracts', compact('contracts'));

    }

    public function updateEndTime(Request $request, $id)
    {
        $contract = EmployeeContract::find($id, ['contract_id']);
        $contract->end_date = $request->end_date;
        $contract->save();

        return redirect()->route('employees.contracts')->with('success', 'Contract ended successfully');
    }

    public function create_employeecontract()
    {
        return view('employees.create_employeecontract', ['employees' => Employee::all()], ['functions' => EmployeeFunction::all()]);
    }

    public function store_contract(Request $request)
    {
        $request->validate([
            'employee' => 'required|integer|min:1',
            'function' => 'required|integer|min:1',
            'start_date' => 'required|date',
        ],
        [
            'employee.required' => 'Employee is required.',
            'employee.integer' => 'Employee must be a number.',
            'function.required' => 'Job is required.',
            'function.integer' => 'Job must be a number.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a date.'
        ]);
    
        $contract = [
            'employee_id' => $request->employee,
            'job_id' => $request->function,
            'start_date' => $request->start_date
        ];
    
        EmployeeContract::create($contract);
        dump($contract);
        return redirect()->route('employees.contracts')->with('success', 'Contract created successfully');
    }
}
