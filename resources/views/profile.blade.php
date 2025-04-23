<x-app-layout>
    @section('title', 'Profile')
    <div class="flex items-center justify-center min-h-[calc(100vh-121px)] bg-gray-100 flex-col">
        <form class="rounded-md shadow border-gray-200 p-4 border " action="{{ route('auth.update') }}" method="POST">
            @csrf
            <div class="flex flex-row justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Profile</h1>
                <a class="text-2xl hover:text-blue-500" href="{{ route('welcome') }}">
                    <x-x-icon></x-x-icon>
                </a>
            </div>
            <div class="flex flex-row align-items-center justify-between mb-4 w-full">
                <div class="w-full mr-2">
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" disabled id="first_name" name="first_name"
                        value="{{ Auth::user()->first_name }}"
                        class="mt-1 disabled: block w-full px-3 py-2 border disabled:bg-gray-200 disabled:cursor-not-allowed border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('first_name')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="w-full ">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" disabled id="last_name" name="last_name" value="{{ Auth::user()->last_name }}"
                        class="mt-1 block w-full px-3 py-2 border disabled:bg-gray-200 disabled:cursor-not-allowed border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('last_name')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="w-full ml-2">
                    <label for="role" class="block text-sm font-medium text-gray-700">Role <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="role" disabled name="role"
                        value="{{ Auth::user()->getRoleNames()->first() }}"
                        class="mt-1 disabled:bg-gray-200 disabled:cursor-not-allowed block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('last_name')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span
                        class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ Auth::user()->email }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('email')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number <span
                        class="text-red-500">*</span></label>
                <input type="text" id="phone_number" name="phone_number" value="{{ Auth::user()->phone_number }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('phone_number')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex flex-row align-items-center justify-between mb-4 w-full">
                <div class="w-full mr-2">
                    <label for="street" class="block text-sm font-medium text-gray-700">Street <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="street" name="street" value="{{ Auth::user()->address->street }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('street')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="ml-2">
                    <label for="house_number" class="block text-sm font-medium text-gray-700">House Number <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="house_number" name="house_number"
                        value="{{ Auth::user()->address->house_number }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('house_number')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="ml-2">
                    <label for="bus_number" class="block text-sm font-medium text-gray-700">Bus Number</label>
                    <input type="text" id="bus_number" name="bus_number"
                        value="{{ Auth::user()->address->bus_number }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="flex flex-row align-items-center justify-between mb-4 w-full">
                <div class="w-full mr-2">
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="postal_code" name="postal_code"
                        value="{{ Auth::user()->address->city->postcode }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('postal_code')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="w-full ml-2">
                    <label for="city" class="block text-sm font-medium text-gray-700">City <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="city" name="city"
                        value="{{ Auth::user()->address->city->name }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('city')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="w-full ml-2">
                    <label for="country" class="block text-sm font-medium text-gray-700">Country <span
                            class="text-red-500">*</span></label>
                    <select name="country" id="country"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select a country</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->country_name }}"
                                {{ Auth::user()->address->city->country->country_name == $country->country_name ? 'selected' : '' }}>
                                {{ $country->country_name }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>



            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Update</button>
        </form>
        @can("token.create")
        <div class="rounded-md mt-2 shadow border-gray-200 p-4 border">
            <button type="submit" onclick="submit()"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Regenerate
                API Token</button>
            <div id="tokens">

            </div>
        </div>
        @endcan
    </div>

    @if (Auth::user()->employee)
    <div class="mt-4">
    @php
        $contract = Auth::user()->employee->contracts
            ->where('employee_id', Auth::user()->employee->id)
            ->where(function ($query) {
                $query->where('end_date', '>', \Carbon\Carbon::now())
                    ->orWhereNull('end_date');
            })
            ->first();

        $created_at = $contract ? $contract->created_at : null;

        $filePath = $created_at 
            ? "contracts/contract_" . Auth::user()->last_name . "_" . Auth::user()->first_name . "_" . $created_at . ".pdf"
            : null;
    @endphp

    @if ($filePath && file_exists(public_path($filePath)))
        <embed src="{{ asset($filePath) }}" type="application/pdf" width="100%" height="600px">
    @else
        <p>Contract not found.</p>
    @endif
    </div>
    @endif

    @can("token.create")
    <script>
        const csrf = "{{ csrf_token() }}";
        const route = "{{ route('tokens.create') }}";

        function submit() {
            fetch(route, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrf,
                    }
                })
                .then((response) => {
                    return response.json();
                })
                .then((data) => {
                    let token = document.createElement("p");
                    token.classList.add("mt-1");
                    token.classList.add("p-2");
                    token.innerText = data.token.substring(2);
                    document.getElementById("tokens").innerHTML = "";
                    document.getElementById("tokens").appendChild(token);
                });
        }
    </script>
    @endcan
</x-app-layout>
