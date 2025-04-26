<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\{User, Employee, EmployeeContract};
use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('welcome');
        }

        $countries = Country::all();
        return view('auth.register', compact('countries'));
    }

    public function showCustomers()
    {
        $countries = Country::all();
        return view('profile', compact('countries'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return redirect()->back()->withErrors(['error' => 'User not found.']);
        }

        // Validate the request data
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|unique:users,phone_number,' . $user->id . '|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|integer',
            'city' => 'required|string|max:100',
            'street' => 'required|string|max:100',
            'house_number' => 'required|integer',
            'bus_number' => 'nullable|string|max:10',
        ]);

        // Update or create the country
        $country = Country::firstOrCreate(['country_name' => $validated['country']], ['country_name' => $validated['country']]);

        // Update or create the city
        $city = City::firstOrCreate([
            'name' => $validated['city'],
            'postcode' => $validated['postal_code'],
            'country_id' => $country->id,
        ]);

        // Update or create the address
        $address = Address::updateOrCreate(
            ['id' => $user->address_id],
            [
                'street' => $validated['street'],
                'house_number' => $validated['house_number'],
                'cities_id' => $city->id,
                'bus_number' => $validated['bus_number'],
            ]
        );

        // Update the user's information
        $user->update([
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'address_id' => $address->id,
        ]);

        return redirect()->back()->with('success', 'User information updated successfully.');
    }

    public function authenticate(Request $request, $route = "welcome")
    {
        $credentials = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

            if ($employee = Employee::where('user_id', $user->id)->first()) {
                $active_contract = EmployeeContract::where('employee_id', $employee->id)
                    ->where(function ($query) {
                        $query->where('end_date', '>', Carbon::now())
                            ->orWhereNull('end_date');
                    })
                    ->first();

                if (!$active_contract) {
                    Log::channel('login')->warning('Unsuccessful login attempt: Contract ended for user ID ' . $user->id);
                    return back()->withErrors([
                        'email' => 'Your contract has ended.',
                    ]);
                } else {
                    if (Auth::attempt($credentials)) {
                        $request->session()->regenerate();
                        Log::channel('login')->info('Successful login: User ID ' . $user->id);
                        return redirect()->route($route);
                    }
                }
            } else {
                if (Auth::attempt($credentials)) {
                    $request->session()->regenerate();
                    Log::channel('login')->info('Successful login: User ID ' . $user->id);
                    return redirect()->route($route);
                }
            }

            Log::channel('login')->warning('Unsuccessful login attempt: Invalid credentials for email ' . $credentials['email']);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function store(Request $request)
    {
        // Determine validation rules based on account type
        $rules = [
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
            'confirm-password' => 'required|same:password',
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users,phone_number',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|integer',
            'city' => 'required|string|max:100',
            'street' => 'required|string|max:100',
            'house_number' => 'required|integer',
            'bus_number' => 'nullable|string|max:10',
        ];
    
        if ($request->account_type === 'individual') {
            $rules = array_merge($rules, [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'birth_date' => 'required|date|before:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            ]);
        } elseif ($request->account_type === 'company') {
            $rules = array_merge($rules, [
                'company_name' => 'required|string|max:255',
                'VAT_Number' => 'required|string|max:50|unique:users,VAT_Number',
            ]);
        }
    
        // Validate the request
        $validated = $request->validate($rules);
    
        // Create or find the country
        $country = Country::firstOrCreate(['country_name' => $validated['country']], ['country_name' => $validated['country']]);
    
        // Create or find the city
        $city = City::firstOrCreate([
            'name' => $validated['city'],
            'postcode' => $validated['postal_code'],
            'country_id' => $country->id,
        ]);
    
        // Create the address
        $address = Address::firstOrCreate([
            'street' => $validated['street'],
            'house_number' => $validated['house_number'],
            'cities_id' => $city->id,
            'bus_number' => $validated['bus_number'],
        ]);
    
        // Prepare user data
        $userData = [
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'address_id' => $address->id,
            'isCompany' => 0, // Default to 0 (individual)
        ];
    
        if ($request->account_type === 'individual') {
            $userData['first_name'] = $validated['first_name'];
            $userData['last_name'] = $validated['last_name'];
            $userData['birth_date'] = $validated['birth_date'];
        } elseif ($request->account_type === 'company') {
            $userData['company_name'] = $validated['company_name'];
            $userData['VAT_Number'] = $validated['VAT_Number']; // Ensure VAT number is saved^
            $userData['isCompany'] = 1;

        }
    
        // Create the user
        Log::info($userData);
        $user = User::create($userData);
    
        // Redirect to the login page
        return redirect('login')->with('success', 'Account created successfully. Please log in.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            Log::channel('login')->info('User signed out: User ID ' . $user->id);
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($request->is('courier/*')) {
            return redirect()->route('courier');
        }
        return redirect('/');
    }
}
