<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\{Employee, Country, City, Address, EmployeeContract, User, EmployeeFunction, Team, Role, Vacation, Location};
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use App\Rules\Validate_Adult;
use Illuminate\Http\Request;
use carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::whereHas('employee', function ($query) {
            $query->whereHas('contracts', function ($subQuery) {
                $subQuery->where('end_date', '>', Carbon::now())->orWhereNull('end_date');
            });
        })->with(['employee', 'employee.contracts', 'employee.team'])->paginate(3);
        return view('employees.index', compact('employees'), ['teams' => Team::all()]);
    }

    public function managerCalendar()
    {
        $employees = Employee::with('user')->get();
        $totalEmployees = $employees->count();

        // Fetch approved holidays and group them by date
        $approvedHolidays = Vacation::where('approve_status', 'Approved')
            ->with(['employee.user'])
            ->get()
            ->groupBy(function ($vacation) {
                return $vacation->start_date; // Group by start_date
            });

        // Calculate availability for each time slot and the whole day
        $availability = [
            'totalEmployees' => $totalEmployees // Include total employees for fallback
        ];
        foreach ($approvedHolidays as $date => $holidays) {
            $morningUnavailable = 0; // 08:00–12:00
            $afternoonUnavailable = 0; // 12:00–17:00
            $fullDayUnavailable = 0; // 08:00–17:00

            foreach ($holidays as $holiday) {
                if ($holiday->day_type === 'Whole Day') {
                    $fullDayUnavailable++;
                } elseif ($holiday->day_type === 'First Half') {
                    $morningUnavailable++;
                } elseif ($holiday->day_type === 'Second Half') {
                    $afternoonUnavailable++;
                }
            }

            $availability[$date] = [
                'morning' => [
                    'available' => $totalEmployees - $morningUnavailable,
                    'percentage' => (($totalEmployees - $morningUnavailable) / $totalEmployees) * 100,
                ],
                'afternoon' => [
                    'available' => $totalEmployees - $afternoonUnavailable,
                    'percentage' => (($totalEmployees - $afternoonUnavailable) / $totalEmployees) * 100,
                ],
                'fullDay' => [
                    'available' => $totalEmployees - $fullDayUnavailable,
                    'percentage' => (($totalEmployees - $fullDayUnavailable) / $totalEmployees) * 100,
                ],
            ];
        }

        // Debugging: Log availability data
        \Log::info('Employee Availability:', $availability);

        return view('employees.manager_calendar', compact('employees', 'totalEmployees', 'approvedHolidays', 'availability'));
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
        $user = Auth::user();
        $location = $user->employee->contracts->location_id;

        $contracts = EmployeeContract::where('end_date', '>', Carbon::now())->orWhereNull('end_date')->where('location_id', $location)->paginate(2);
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
        $locations = Location::whereNot('location_type', 'ADDRESS')->get();
        $employees = Employee::all();
        $functions = EmployeeFunction::all();
    
        return view('employees.create_employeecontract', compact('locations', 'employees', 'functions'));
    }

    public function store_contract(Request $request)
    {
        $request->validate([
            'employee' => 'required|integer|min:1',
            'function' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'vacation_days' => 'required|integer|min:0',
            'location' => 'required|integer|min:1',
        ],
        [
            'employee.required' => 'Employee is required.',
            'employee.min' => 'Please select an employee.',
            'function.required' => 'Job is required.',
            'function.min' => 'Please select a job.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a date.',
            'vacation_days.required' => 'It would be nice if the employee could have some vacation days.',
            'vacation_days.min' => 'Cannot give a negative amount of vacation days.',
            'location.required' => 'Location is required.',
            'location.min' => 'Please select a location.',
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
                'start_date' => $request->start_date,
                'location_id' => $request->location,
            ];
            
            $employee = Employee::find($request->employee);
            $employee->leave_balance = $request->vacation_days;
            $employee->save();
    
            EmployeeContract::create($contract);

            $role = EmployeeFunction::find($request->function)->role;
            $user = User::find($employee->user_id);
            $user->syncRoles([]);
            $user->assignRole($role);

            return redirect()->route('employees.contracts')->with('success', 'Contract created successfully');
        }
        else {
            return redirect()->route('employees.contracts')->with('error', 'Employee already has a contract');
        }
    }

    public function teams()
    {
        $teams = Team::paginate(3);
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
        $functions = EmployeeFunction::paginate(3);
        return view('employees.functions', compact('functions'));
    }

    public function create_function()
    {
        return view('employees.create_function', ['roles' => Role::all()]);
    }

    public function store_function(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:functions,name',
            'description' => 'required|string',
            'salary_min' => 'required|numeric|min:0|lt:salary_max|max:999999',
            'salary_max' => 'required|numeric|min:0|gt:salary_min|max:999999',
            'role' => 'required|integer|min:1',
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
            'role.required' => 'Role is required.',
            'role.min' => 'Please select a role.',
        ]);

        $role = Role::find($request->role);

        $function = [
            "name" => $request->name,
            "description" => $request->description,
            "salary_min" => $request->salary_min,
            "salary_max" => $request->salary_max,
            "role" => $role->name
        ];

        EmployeeFunction::create($function);
        return redirect()->route('employees.functions')->with('success', 'Function created successfully');
    }

    public function generateEmployeeContract($id)
    {
        if (!Auth::check()) {
            abort(401, 'Unauthorized access');
        }

        $contract = EmployeeContract::with([
            'employee',
            'function'
        ])->findOrFail($id);

            $data = [
                'contract' => $contract,
                'employer' => $contract->employee->team->manager->user,
                'employer_address' => $contract->employee->team->manager->user->address,
                'employee' => $contract->employee->user,
                'employee_address' => $contract->employee->user->address,
                'function' => $contract->function,
            ];

        $pdf = Pdf::loadView('employees.employee-contract-template', $data);
        return $pdf->stream('employee-contract.pdf');
    }

    public function getAvailabilityData(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->query('end_date', now()->endOfWeek()->toDateString());

        if (!$startDate || !$endDate) {
            return response()->json(['error' => 'Invalid date range provided.'], 400);
        }

        try {
            $availabilityData = [];
            $currentDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);

            while ($currentDate->lte($endDate)) {
                $date = $currentDate->toDateString();

                $totalEmployees = \App\Models\Employee::count();

                $onHolidayCount = \App\Models\Vacation::where('vacation_type', 'Holiday')
                    ->where('approve_status', 'Approved')
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->count();

                $sickCount = \App\Models\Vacation::where('vacation_type', 'Sick Leave')
                    ->where('approve_status', 'Approved')
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->count();

                $availableCount = $totalEmployees - $onHolidayCount - $sickCount;

                $availabilityData[] = [
                    'date' => $date,
                    'available' => $availableCount,
                    'onHoliday' => $onHolidayCount,
                    'sick' => $sickCount,
                ];

                $currentDate->addDay();
            }

            return response()->json($availabilityData);
        } catch (\Exception $e) {
            \Log::error('Error fetching availability data:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while fetching availability data.'], 500);
        }
    }

    public function getUnavailableEmployees(Request $request)
    {
        $date = $request->query('date');

        if (!$date) {
            return response()->json(['error' => 'Date parameter is required.'], 400);
        }

        $onHoliday = Vacation::where('vacation_type', 'Holiday')
            ->where('approve_status', 'Approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->with('employee.user')
            ->get()
            ->map(function ($vacation) {
                return optional($vacation->employee->user)->first_name . ' ' . optional($vacation->employee->user)->last_name;
            });

        $sick = Vacation::where('vacation_type', 'Sick Leave')
            ->where('approve_status', 'Approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->with('employee.user')
            ->get()
            ->map(function ($vacation) {
                return optional($vacation->employee->user)->first_name . ' ' . optional($vacation->employee->user)->last_name;
            });

        return response()->json([
            'onHoliday' => $onHoliday,
            'sick' => $sick,
        ]);
    }
}
