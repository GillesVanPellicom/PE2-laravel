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
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if($user->hasRole(['HRManager', 'admin']))
        {
            /*$employees = User::whereHas('employee', function ($query) {
                $query->whereHas('contracts', function ($subQuery) {
                    $subQuery->where('end_date', '>', Carbon::now())->orWhereNull('end_date');
                });
            })->with(['employee', 'employee.contracts', 'employee.team'])->paginate(env('EMPLOYEE_PAGINATE'));*/

            $employees = User::whereHas('employee')->with(['employee', 'employee.contracts', 'employee.team'])->paginate(env('EMPLOYEE_PAGINATE'));
            return view('employees.index', compact('employees'), ['teams' => Team::all()]);
        }

        /*$employees = User::whereHas('employee', function ($query) {
            $query->where('location_id', Auth::user()->employee->contracts->location_id);
            $query->whereHas('contracts', function ($subQuery) {
                $subQuery->where('end_date', '>', Carbon::now())->orWhereNull('end_date')->where('location_id', Auth::user()->employee->contracts->location_id);
            });
        })->with(['employee', 'employee.contracts', 'employee.team'])->paginate(env('EMPLOYEE_PAGINATE'));*/

        $employees = User::whereHas('employee.contracts', function ($query) {
            $query->where('location_id', Auth::user()->employee->contracts->location_id);
        })->with(['employee', 'employee.contracts', 'employee.team'])->paginate(env('EMPLOYEE_PAGINATE'));

        return view('employees.index', compact('employees'), ['teams' => Team::all()]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $active = $request->input('active');
        $user = Auth::user();
        $location = "yes";

        if($user->hasRole(['HRManager', 'admin'])) {
            $location = null;
        }

        $employeesQuery = User::whereHas('employee', function ($q) use ($active) {
            if ($active != 1) {
                if ($active == 2) {
                    $q->whereHas('contracts', function ($subQuery) {
                        $subQuery->where('end_date', '>', now())->orWhereNull('end_date');
                    });
                } else {
                    $q->whereDoesntHave('contracts') // Include employees without contracts
                      ->orWhereHas('contracts', function ($subQuery) {
                          $subQuery->where('end_date', '<', now());
                      });
                }
            }
        })
        ->where(function ($q) use ($query) {
            $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$query%"])
              ->orwhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%$query%"])
              ->orWhere('email', 'like', "%$query%");
        })
        ->with(['employee', 'employee.contracts', 'employee.team', 'address', 'address.city', 'address.city.country']);

        if($location != null) {
            $employeesQuery->whereHas('employee.contracts', function ($q) use ($user) {
                $q->where('location_id', $user->employee->contracts->location_id);
            });
        }

        // Limit to 3 employees if filters are at default values
        if (empty($query) && $active == "1") {
            $employees = $employeesQuery->take(env('EMPLOYEE_PAGINATE'))->get();
        } else {
            $employees = $employeesQuery->get();
        }

        return response()->json(['employees' => $employees]);
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

        return redirect()->route('workspace.employees.index')->with('success', 'Employee created successfully');;
    }

    public function contracts()
    {
        $user = Auth::user();
        if($user->hasRole(['HRManager', 'admin']))
        {
            /*where('end_date', '>', Carbon::now())->orWhereNull('end_date')*/
            $contracts = EmployeeContract::paginate(env('EMPLOYEE_PAGINATE'));
            return view('employees.contracts', compact('contracts'));
        }
        $location = $user->employee->contracts->location_id;

        $contracts = EmployeeContract::where('end_date', '>', Carbon::now())->orWhereNull('end_date')->where('location_id', $location)->paginate(env('EMPLOYEE_PAGINATE'));
        return view('employees.contracts', compact('contracts'));
    }

    public function searchContract(Request $request)
    {
        $query = $request->input('query');
        $active = $request->input('active');

        $contractsQuery = EmployeeContract::whereHas('employee.user', function ($q) use ($query) {
            $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$query%"])
              ->orWhere('email', 'like', "%$query%");
        });

        $user = Auth::user();
        if (!$user->hasRole(['HRManager', 'admin'])) {
            $locationId = $user->employee->contracts->location_id;
            $contractsQuery->where('location_id', $locationId);
        }

        if ($active !== null) {
            if ($active == 2) { // Active contracts
                $contractsQuery->where('start_date', '<=', now())
                               ->where(function ($q) {
                                   $q->where('end_date', '>', now())
                                     ->orWhereNull('end_date');
                               });
            } elseif ($active == 3) { // Ended contracts
                $contractsQuery->where('end_date', '<', now());
            } elseif ($active == 4) { // Future contracts
                $contractsQuery->where('start_date', '>', now());
            }
        }

        if (empty($query) && $active == "1") {
            $contracts = $contractsQuery->with(['employee.user', 'function', 'location'])->paginate(env('EMPLOYEE_PAGINATE'));
        } else {
            $contracts = $contractsQuery->with(['employee.user', 'function', 'location'])->get();
        }


        return response()->json(['contracts' => $contracts]);
    }

    public function updateEndTime(Request $request, $id)
    {

        $request->validate([
            'end_date' => 'required|date|after_or_equal:' . now()->format('Y-m-d'),
        ],
        [
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a date.',
            'end_date.after_or_equal' => 'cannot end a contract in the past.',
        ]);

        $contract = EmployeeContract::find($id, ['contract_id']);
        $contract->end_date = $request->end_date;
        $contract->save();

        return redirect()->route('workspace.employees.contracts')->with('success', 'Contract ended successfully');
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
            'start_date' => [
                'required',
                'date',
                'after_or_equal:' . now()->startOfMonth()->format('Y-m-d')
            ],
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
            'start_date.after_or_equal' => 'Start date must be on or after the beginning of the current month.',
            'vacation_days.required' => 'It would be nice if the employee could have some vacation days.',
            'vacation_days.min' => 'Cannot give a negative amount of vacation days.',
            'location.required' => 'Location is required.',
            'location.min' => 'Please select a location.',
        ]);

        $active_contract = EmployeeContract::where('employee_id', $request->employee)
            ->where(function ($query) use ($request) {
                $query->where('end_date', '>', $request->start_date)
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

            $cont = EmployeeContract::create($contract);

            $role = EmployeeFunction::find($request->function)->role;
            $user = User::find($employee->user_id);
            $user->syncRoles([]);
            $user->assignRole($role);

            EmployeeController::generateEmployeeContract($cont->contract_id);

            return redirect()->route('workspace.employees.contracts')->with('success', 'Contract created successfully');
        }
        else {
            return redirect()->route('workspace.employees.contracts')->with('error', 'Employee already has a contract');
        }
    }

    public function teams()
    {
        $teams = Team::paginate(env('EMPLOYEE_PAGINATE'));
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
        return redirect()->route('workspace.employees.teams')->with('success', 'Team created successfully');
    }

    public function functions()
    {
        $functions = EmployeeFunction::paginate(env('EMPLOYEE_PAGINATE'));
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
        return redirect()->route('workspace.employees.functions')->with('success', 'Function created successfully');
    }

    public static function generateEmployeeContract($id)
    {

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

        $contractPath = public_path('contracts');
        if (!file_exists($contractPath)) {
            mkdir($contractPath, 0777, true); // Create the directory with full permissions
        }

        $timestamp = $contract->created_at;
        $filename = "contract_{$contract->employee->user->last_name}_{$contract->employee->user->first_name}_{$timestamp}";
        $pdf->save(public_path("contracts/{$filename}.pdf"));
        return redirect()->route('workspace.employees.contracts')->with('success', 'Contract created successfully')->with('pdf_url', url("contracts/{$filename}.pdf"));
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

        // Fetch all employees
        $allEmployees = Employee::with('user')->get();

        // Fetch employees on holiday
        $onHoliday = Vacation::where('vacation_type', 'Holiday')
            ->where('approve_status', 'Approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->with('employee.user')
            ->get()
            ->map(function ($vacation) {
                return [
                    'id' => $vacation->employee->id,
                    'name' => $vacation->employee->user->first_name . ' ' . $vacation->employee->user->last_name,
                ];
            });

        // Fetch sick employees
        $sick = Vacation::where('vacation_type', 'Sick Leave')
            ->where('approve_status', 'Approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->with('employee.user')
            ->get()
            ->map(function ($vacation) {
                return [
                    'id' => $vacation->employee->id,
                    'name' => $vacation->employee->user->first_name . ' ' . $vacation->employee->user->last_name,
                ];
            });

        // Filter available employees (exclude sick and holiday employees)
        $unavailableIds = $onHoliday->pluck('id')->merge($sick->pluck('id'))->toArray();
        $available = $allEmployees->filter(function ($employee) use ($unavailableIds) {
            return !in_array($employee->id, $unavailableIds);
        })->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->user->first_name . ' ' . $employee->user->last_name,
            ];
        });

        return response()->json([
            'available' => $available->values(),
            'sick' => $sick->values(),
            'holiday' => $onHoliday->values(),
        ]);
    }
}
