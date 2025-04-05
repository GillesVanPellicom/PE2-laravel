<x-app-layout>
    @section('title', 'Register')
    <div class="flex items-center justify-center min-h-[calc(100vh-121px)] bg-gray-100">
        <form action="{{ route('auth.store') }}" method="POST" class="bg-white p-8 rounded shadow-md w-full max-w-xl">
            @csrf
            <div class="flex flex-row justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Create an Account</h1>
                <a class="text-2xl hover:text-blue-500" href="{{ route('welcome') }}">
                    <x-x-icon></x-x-icon>
                </a>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email address <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('email')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex flex-row align-items-center justify-between mb-4 w-full">
                <div class="w-full mr-2">
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('first_name')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="w-full ml-2">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('last_name')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('password')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" id="confirm-password" name="confirm-password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('confirm-password')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="country" class="block text-sm font-medium text-gray-700">Country <span class="text-red-500">*</span></label>
                <select name="country" id="country" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Select a country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->country_name }}" {{ old('country') == $country->country_name ? 'selected' : '' }}>{{ $country->country_name }}</option>
                    @endforeach
                </select>
                @error('country')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number <span class="text-red-500">*</span></label>
                <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('phone_number')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="birth_date" class="block text-sm font-medium text-gray-700">Birth Date <span class="text-red-500">*</span></label>
                <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" max="{{ \Carbon\Carbon::now()->subYears(18)->format('Y-m-d') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('birth_date')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex flex-row justify-between mb-6">
                <div class="w-11/12 mr-2">
                    <label for="street" class="block text-sm font-medium text-gray-700">Street <span class="text-red-500">*</span></label>
                    <input type="text" id="street" name="street" value="{{ old('street') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('street')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class=" ml-2">
                    <label for="house_number" class="block text-sm font-medium text-gray-700">House Number <span class="text-red-500">*</span></label>
                    <input type="text" id="house_number" name="house_number" value="{{ old('house_number') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('house_number')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="ml-2">
                    <label for="bus_number" class="block text-sm font-medium text-gray-700">Bus Number</label>
                    <input type="text" id="bus_number" name="bus_number" value="{{ old('bus_number') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('bus_number')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

            </div>
            <div class="flex flex-row justify-between mb-4">
                <div class="w-full mr-2">
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code <span class="text-red-500">*</span></label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('postal_code')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="w-full ml-2">
                    <label for="city" class="block text-sm font-medium text-gray-700">City <span class="text-red-500">*</span></label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('city')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Register</button>
        </form>
    </div>
</x-app-layout>
