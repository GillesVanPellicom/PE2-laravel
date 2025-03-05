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
        $employee = $request->validate([
            'name' => 'required|string',
            'firstname' => 'required|string',
            'email' => 'required|email:rfc',
            'birthdate' => ['required', 'date', 'before:today', 'after:1900-01-01', new Validate_Adult('employee')],
            // just in case we want to be able to add employees that are not yet hired or were hired in the past
            'hire_date' => 'nullable|after_or_equal:birthdate|before_or_equal:today',
            'vacation_days' => 'nullable|integer',  //removing integer doesnt work
        ],
        [
            'name.required' => 'Name is required',
            'firstname.required' => 'Firstname is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is not valid',
            'birthdate.required' => 'Birthdate is required',
            'hire_date.required' => 'Hire date is required',
            'birthdate.before' => 'The birthdate cannot be a future date.',
            'birthdate.after' => 'The birthdate must be after 1900-01-01.',
            //'birthdate.date' => 'The birthdate must be a date.',
            //'hire_date.date' => 'The hire date must be a date.',
            'hire_date.after_or_equal' => 'The hire date must be after or equal to the birthdate.',
            'hire_date.before_or_equal' => 'The hire date cannot be a future date.',
        ]);

        $new_employee = Employee::create($employee);

        return redirect()->route('employees.index');
    }
}
