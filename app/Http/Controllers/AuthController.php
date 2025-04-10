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
                    return back()->withErrors([
                        'email' => 'Your contract has ended.',
                    ]);
                } else {
                    if (Auth::attempt($credentials)) {
                        $request->session()->regenerate();
                        return redirect()->route($route);
                    }
                }
            } else {
                if (Auth::attempt($credentials)) {
                    $request->session()->regenerate();
                    return redirect()->route($route);
                }
            }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
            'confirm-password' => 'required|same:password',
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users,phone_number',
            'birth_date' => 'required|date|before:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            'country' => 'required|string|max:100',
            'postal_code' => 'required|integer',
            'city' => 'required|string|max:100',
            'street' => 'required|string|max:100',
            'house_number' => 'required|integer',
            'bus_number' => 'nullable|string|max:10',
        ]);

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

        // Create the user with the address_id
        $userData = $request->only(['first_name', 'last_name', 'email', 'phone_number', 'birth_date']);
        $userData['password'] = Hash::make($request->password);
        $userData['address_id'] = $address->id;

        $user = User::create($userData);

         // event(new Registered($user));
         // Email verification has been disabled for now

        // Redirect to the login page
        return redirect('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($request->is('courier/*')) {
            return redirect()->route('courier');
        }
        return redirect('/');
    }
}
