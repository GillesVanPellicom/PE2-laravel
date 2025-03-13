<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
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
        $countries = Country::all();
        return view('auth.register', compact('countries'));
    }

    public function showCustomers()
    {
        $countries = Country::all();
        return view('customers', compact('countries'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return redirect()->back()->withErrors(['error' => 'User not found.']);
        }

        // Validate the request data
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'birth_date' => 'required|date',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'street' => 'required|string|max:100',
            'house_number' => 'required|string|max:100',
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
            ]
        );

        // Update the user's information
        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'birth_date' => $validated['birth_date'],
            'address_id' => $address->id,
        ]);

        return redirect()->back()->with('success', 'User information updated successfully.');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('customers');
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
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'birth_date' => 'required|date|before:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'street' => 'required|string|max:100',
            'house_number' => 'required|string|max:100',
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
        $address = Address::create([
            'street' => $validated['street'],
            'house_number' => $validated['house_number'],
            'cities_id' => $city->id,
        ]);

        // Create the user with the address_id
        $userData = $request->only(['first_name', 'last_name', 'email', 'phone_number', 'birth_date']);
        $userData['password'] = Hash::make($request->password);
        $userData['address_id'] = $address->id;

        User::create($userData);

        // Redirect to the login page
        return redirect('/login');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
