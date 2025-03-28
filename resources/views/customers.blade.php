<x-app-layout>
    <main class="flex items-center justify-center min-h-screen">
        <div class="mt-4 mb-4 bg-white p-8 rounded shadow-md w-full max-w-2xl">
            <h1 class="text-2xl font-bold mb-6">Customers</h1>
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-700">Your role: <span class="font-normal">{{ Auth::user()->role }}</span></p>
                <p class="text-sm font-medium text-gray-700">Your first name: <span class="font-normal">{{ Auth::user()->first_name }}</span></p>
                <p class="text-sm font-medium text-gray-700">Your last name: <span class="font-normal">{{ Auth::user()->last_name }}</span></p>
                <p class="text-sm font-medium text-gray-700">Your email: <span class="font-normal">{{ Auth::user()->email }}</span></p>
                <p class="text-sm font-medium text-gray-700">Your birth date: <span class="font-normal">{{ Auth::user()->birth_date }}</span></p>
                <p class="text-sm font-medium text-gray-700">Your country: <span class="font-normal">{{ Auth::user()->address->city->country->country_name }}</span></p>
                <p class="text-sm font-medium text-gray-700">Your postal code: <span class="font-normal">{{ Auth::user()->address->city->postcode }}</span></p>
                <p class="text-sm font-medium text-gray-700">Your city: <span class="font-normal">{{ Auth::user()->address->city->name }}</span></p>
                <p class="text-sm font-medium text-gray-700">Your street: <span class="font-normal">{{ Auth::user()->address->street }}</span></p>
                <p class="text-sm font-medium text-gray-700">Your house number: <span class="font-normal">{{ Auth::user()->address->house_number }}</span></p>
                <p class="text-sm font-medium text-gray-700">Your bus number: <span class="font-normal">{{ Auth::user()->address->bus_number }}</span></p>
                <p class="text-sm font-medium text-gray-700">Your phone number: <span class="font-normal">{{ Auth::user()->phone_number }}</span></p>
            </div>

            <form action="{{ route('auth.logout') }}" method="POST" class="mb-6">
                @csrf
                <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Sign Out</button>
            </form>
            <form action="{{ route('auth.update') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="country" class="block text-sm font-medium text-gray-700">Country <span class="text-red-500">*</span></label>
                    <select name="country" id="country" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select a country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_name }}" {{ Auth::user()->address->city->country->country_name == $country->country_name ? 'selected' : '' }}>{{ $country->country_name }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ Auth::user()->phone_number }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('phone_number')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code <span class="text-red-500">*</span></label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ Auth::user()->address->city->postcode }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('postal_code')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="city" class="block text-sm font-medium text-gray-700">City <span class="text-red-500">*</span></label>
                    <input type="text" id="city" name="city" value="{{ Auth::user()->address->city->name }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('city')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="street" class="block text-sm font-medium text-gray-700">Street <span class="text-red-500">*</span></label>
                    <input type="text" id="street" name="street" value="{{ Auth::user()->address->street }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('street')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="house_number" class="block text-sm font-medium text-gray-700">House Number <span class="text-red-500">*</span></label>
                    <input type="text" id="house_number" name="house_number" value="{{ Auth::user()->address->house_number }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('house_number')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="bus_number" class="block text-sm font-medium text-gray-700">Bus Number</label>
                    <input type="text" id="bus_number" name="bus_number" value="{{ Auth::user()->address->bus_number }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Update</button>
                <a href="{{ route('welcome') }}" class="mt-4 inline-block w-full text-center bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">Return to homepage</a>
            </form>
        </div>
    </main>
</x-app-layout>