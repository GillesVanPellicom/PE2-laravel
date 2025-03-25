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
        })->with(['employee', 'employee.contracts', 'employee.team'])->get();
        return view('employees.index', compact('employees'), ['teams' => Team::all()]);
    }

    public function managerCalendar()
    {
        $employees = Employee::with('user')->get(); // Fetch employees + user data
        return view('employees.manager_calendar', compact('employees')); 
    }

    public function holidayRequest()
    {
        $employees = Employee::with('user')->get(); // Fetch employees with user data
        return view('holiday-request', compact('employees'));
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
            'Apartment_number' => 'nullable|alpha',
            'city' => 'required|string',
            'postcode' => 'required|integer',

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
            'city.string' => 'City must be a string',

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

        $country = Country::where('country_name', $request->country)->first();
        $city = City::where('name', $request->city)->where('country_id', $country->id)->first();

        if (!$city) 
        {
            $city = City::create([
                'name' => $request->city,
                'country_id' => $country->id,
                'postcode' => $request->postcode,
            ]);
        }

        $address = [
            'street' => $request->street,
            'house_number' => $request->house_number,
            'bus_number' => strtoupper($request->bus_number),
            'cities_id' => $city->id,
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
            'vacation_days' => 'required|integer|min:0',
        ],
        [
            'employee.required' => 'Employee is required.',
            'employee.integer' => 'Employee must be a number.',
            'function.required' => 'Job is required.',
            'function.integer' => 'Job must be a number.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a date.',
            'vacation_days.required' => 'It would be nice if the employee could have some vacation days.',
            'vacation_days.min' => 'Cannot give a negative amount of vacation days.',
        ]);
    
        $active_contract = EmployeeContract::where('employee_id', $request->employee)->where(function ($query) 
        {
            $query->where('end_date', '>', Carbon::now())
                  ->orWhereNull('end_date');
        })->first();

        if($active_contract == NULL) {
            $contract = [
                'employee_id' => $request->employee,
                'job_id' => $request->function,
                'start_date' => $request->start_date
            ];
            
            $employee = Employee::find($request->employee);
            $employee->leave_balance = $request->vacation_days;
            $employee->save();
    
            EmployeeContract::create($contract);
            return redirect()->route('employees.contracts')->with('success', 'Contract created successfully');
        }
        else {
            return redirect()->route('employees.contracts')->with('error', 'Employee already has a contract');
        }
    }

    public function teams()
    {
        $teams = Team::all();
        return view('employees.teams', compact('teams'));
    }

    public function create_team()
    {
        $employees = Employee::all();
        return view('employees.create_team', compact('employees'));
    }

    public function store_team(Request $request)
    {
        $request->validate([
            'department' => 'required|string|max:255|unique:teams,department',
            'employee' => 'required|integer|min:1',
        ],
        [
            'department.required' => 'Department is required.',
            'department.string' => 'Department must be a string.',
            'department.max' => 'Department name is too long.',
            'department.unique' => 'Department already exists.',

            'employee.required' => 'Manager is required.',
            'employee.min' => 'Please select a manager',
        ]);

        $team = [
            'department' => $request->department,
            'manager_id' => $request->employee,
        ];

        Team::create($team);
        return redirect()->route('employees.teams')->with('success', 'Team created successfully');
    }

    public function functions()
    {
        $functions = EmployeeFunction::all();
        return view('employees.functions', compact('functions'));
    }

    public function create_function()
    {
        return view('employees.create_function');
    }

    public function store_function(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:functions,name',
            'description' => 'required|string',
            'salary_min' => 'required|numeric|min:0|lt:salary_max|max:999999',
            'salary_max' => 'required|numeric|min:0|gt:salary_min|max:999999',
        ],
        [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name is too long.',
            'name.unique' => 'Name already exists.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a string.',
            'salary_min.required' => 'Minimum salary is required.',
            'salary_min.numeric' => 'Minimum salary must be a number.',
            'salary_min.min' => 'Minimum salary must be larger than 0.',
            'salary_min.lt' => 'Minimum salary must be lower than maximum salary.',
            'salary_min.max' => 'Minimum salary is too high.',
            'salary_max.required' => 'Maximum salary is required.',
            'salary_max.numeric' => 'Maximum salary must be a number.',
            'salary_max.min' => 'Maximum salary must be larger than 0.',
            'salary_max.gt' => 'Maximum salary must be higher than minimum salary.',
            'salary_max.max' => 'Maximum salary is too high.',
        ]);

        EmployeeFunction::create($request->all());
        return redirect()->route('employees.functions')->with('success', 'Function created successfully');
    }

    public function upgrade()
    {
        $employee = User::where('email', "jane.doe@example.com")->first();
        $employee->assignRole('employee');
        return redirect()->route('employees.index')->with('success', 'Employee upgraded successfully');
    }
}
