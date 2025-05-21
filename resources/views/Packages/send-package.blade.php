<x-app-layout>
    @section('title', 'Send a Parcel')
    <style>
        .loader {
            width: 24px;
            height: 24px;
            border: 3px solid #e5e7eb;
            border-radius: 50%;
            border-top: 3px solid #2563eb;
            -webkit-animation: spin 1s linear infinite;
            animation: spin 1s linear infinite;
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <div class="min-h-screen bg-gray-100 py-12">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200">
                    <h2 class="text-3xl font-bold text-gray-800">Send a Parcel</h2>
                    <p class="mt-2 text-gray-600">Fill in the details to send your parcel</p>

                    <div id="updatedDeliveryPrice"></div>
                    <div id="updatedTotalPrice"></div>
                </div>

                @if($errors->any())
                    <div class="px-8 py-4 bg-red-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="px-8 py-4 bg-green-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('package.store') }}" method="POST" class="px-8 py-6">
                    @csrf
                    <div class="space-y-6">
                        @if(!Auth::check())
                            <!-- Sender Information -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Sender Information</h3>
                                <div class="w-full flex flex-col gap-4">
                                    <div class="w-full flex flex-col gap-4">
                                        <div class="w-full flex flex-row gap-4">
                                            <input type="text" name="sender_firstname" placeholder="Name" value="{{old("sender_firstname")}}" required
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                            <input type="text" name="sender_lastname" placeholder="Last Name" value="{{old("sender_lastname")}}" required
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="w-full flex flex-row gap-4">
                                            <input type="email" name="sender_email" placeholder="Email" value="{{old("sender_email")}}" required
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                            <input type="text" name="sender_phone_number" placeholder="Phone Number" value="{{old("sender_phone_number")}}" required
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>

                                    <div class="w-full flex flex-row gap-4">
                                        <input type="text" name="sender_address_input" placeholder="Street" value="{{old("sender_address_input")}}" required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <input type="text" name="sender_house_number" placeholder="House Number" value="{{ old('sender_house_number') }}" required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <input type="text" name="sender_bus_number" placeholder="Bus" value="{{ old('sender_bus_number') }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div class="w-full flex flex-row gap-4">
                                        <input type="text" name="sender_postal_code" placeholder="Postal Code" value="{{ old('sender_postal_code') }}" required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <input type="text" name="sender_city_name" placeholder="City" value="{{ old('sender_city_name') }}" required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">

                                        <select name="sender_country_name" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                            <option value="" disabled selected>Select a country</option>
                                            <option value="Belgium" {{ old('sender_country_name', 'Belgium') == 'Belgium' ? 'selected' : '' }}>Belgium</option>
                                            @foreach($countries as $country)
                                                @if($country->country_name !== 'Belgium')
                                                    <option value="{{ $country->country_name }}" {{ old('sender_country_name') == $country->country_name ? 'selected' : '' }}>
                                                        {{ $country->country_name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-full flex flex-col gap-4">
                                        <div>
                                            <label for="checked_on_create_account" class="text-sm text-gray-700">Create an account?</label>
                                            <input type="checkbox" name="checked_on_create_account" id="checked_on_create_account" onchange="togglePasswordFields()" >
                                        </div>
                                        <div id="passwordFields" class="w-full flex flex-row gap-4" style="display: none;">
                                            <input type="password" name="password" placeholder="Password"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                            <input type="password" name="password_confirmation" placeholder="Confirm Password"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endif

                        <!-- Receiver Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Receiver Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="lastName" value="{{ old('lastName') }}" placeholder="Last Name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="email" name="receiverEmail" value="{{ old('receiverEmail') }}" placeholder="Email" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="receiver_phone_number" value="{{ old('receiver_phone_number') }}" placeholder="Phone Number" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Package Details -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Parcel Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="dimension" value="{{old('dimension')}}" placeholder="Dimension" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Weight Class Selection -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Weight Class</h3>
                            <div class="space-y-3">
                                @foreach($weightClasses as $weightClass)
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="weight_id" value="{{$weightClass->id }}"
                                            data-price="{{ $weightClass->price }}"
                                            onchange="updatePrices()"
                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"

                                               {{ $loop->first && !old('weight_id') ? 'checked' : (old('weight_id') == $weightClass->id ? 'checked' : '') }}

                                        <label class="ml-3 text-sm text-gray-700">
                                              {{$weightClass->name }} ({{ $weightClass->weight_min }} - {{ $weightClass->weight_max }} kg) - €{{ number_format($weightClass->price, 2) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Delivery Method Selection -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Method</h3>
                            <div class="space-y-3">
                                @foreach($deliveryMethods as $method)
                                    <div class="flex items-center">
                                        <input type="radio" name="delivery_method_id" value="{{ $method->id }}"
                                            data-requires-location="{{ $method->requires_location }}"
                                            data-code="{{ $method->code }}"
                                            data-price="{{ $method->price }}"
                                            onchange="handleDeliveryMethodChange(this)"
                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                            {{ old('delivery_method_id') == $method->id ? 'checked' : '' }}>
                                        <label class="ml-3 text-sm text-gray-700">
                                            {{ $method->name }} - €{{ number_format($method->price, 2) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Signature Requirement -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Signature Requirement</h3>
                            <div class="flex items-center">
                                <input type="checkbox" name="requires_signature" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <label class="ml-3 text-sm text-gray-700">Require signature upon delivery</label>
                            </div>
                        </div>

                        <!-- Safe package location-->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Safe Package Location</h3>
                            <div class="flex items-center">
                                <input type="text" name="safe_location" value="{{old('safe_location')}}" placeholder="Eg. Behind the shed."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">

                            </div>
                        </div>

                        <!-- Dynamic Location Section -->
                        <div id="locationSection" style="display: none;" class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Select a Location</h3>
                            <select onchange="updatePrices()" name="destination_location_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select a location</option>
                                @foreach($deliveryMethods as $method)
                                    <optgroup label="{{ $method->name }}" data-code="{{ $method->code }}" style="display: none;">
                                        @foreach($locations->where('location_type', $method->code) as $location)
                                            <option value="{{ $location->id }}" {{ old('destination_location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->description }} - {{ $location->address->street }} {{ $location->address->house_number }}
                                                @if ($location->address->bus_number)
                                                    - {{ $location->address->bus_number }}
                                                @endif
                                                , {{ $location->address->city->postcode }} {{ $location->address->city->name }}, {{ $location->address->city->country->name }}
                                                ({{ $location->opening_hours }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- Address Section -->
                        <div id="addressSection" style="display: none;" class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Address</h3>
                            <!-- Changed from grid to full width -->
                            <div class="w-full">
                                <div class="relative">
                                    <input
                                        id="addressInput"
                                        type="text"
                                        name="addressInput"
                                        onchange="updatePrices()"
                                        placeholder="Enter your address"
                                        autocomplete="off"
                                        value="{{ old('addressInput') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    <!-- Suggestions dropdown -->
                                    <div id="suggestions"
                                        class="absolute w-full top-full left-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg z-50 hidden max-h-60 overflow-y-auto">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Price Summary -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Price Summary</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Weight Price:</span>
                                    <span id="weightPrice" class="font-medium">€0.00</span>
                                    <input type="hidden" name="weight_price" value="0">
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Delivery Price:</span>
                                    <span id="deliveryPrice" class="font-medium">€0.00</span>
                                    <input type="hidden" name="delivery_price" value="0">
                                </div>
                                <div class="pt-3 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-900 font-medium">Total Price:</span>
                                        <div class="flex items-center">
                                            <div id="loadingSpinner" class="hidden">
                                                <div class="loader"></div>
                                            </div>
                                            <span id="totalPrice" class="text-gray-900 font-medium">€0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" id="sendpackage"
                                class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Send Package
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>

        document.querySelector('form').addEventListener('submit', function(e) {
            const selectedDeliveryMethod = document.querySelector('input[name="delivery_method_id"]:checked');
            if (!selectedDeliveryMethod) {
                e.preventDefault();
                alert('Please select a delivery method');
                return;
            }

            const weightInput = document.querySelector('input[name="weight_id"]:checked');
            if (!weightInput) {
                e.preventDefault();
                alert('Please select a weight class');
                return;
            }

            const requiresLocation = selectedDeliveryMethod.dataset.requiresLocation === "1";
            if (requiresLocation) {
                const locationSelect = document.querySelector('select[name="destination_location_id"]');
                if (!locationSelect.value) {
                    e.preventDefault();
                    alert('Please select a destination location');
                    return;
                }
            } else {
                const street = document.querySelector('input[name="street"]').value;
                const houseNumber = document.querySelector('input[name="house_number"]').value;
                const cityId = document.querySelector('input[name="cities_id"]').value;
                const countryId = document.querySelector('input[name="country_id"]').value;

                if (!street || !houseNumber || !cityId || !countryId) {
                    e.preventDefault();
                    alert('Please fill in all address fields');
                    return;
                }
            }
        });

        // Initialize the form state
        document.addEventListener('DOMContentLoaded', function() {
            const checkedDeliveryMethod = document.querySelector('input[name="delivery_method_id"]:checked');
            if (checkedDeliveryMethod) {
                handleDeliveryMethodChange(checkedDeliveryMethod);
            }
            updatePrices();
        });


        document.addEventListener('DOMContentLoaded', function() {
    const addressInput = document.getElementById('addressInput');
    const suggestionsDiv = document.getElementById('suggestions');
    let debounceTimer;

    // Setup address autocomplete
    addressInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);

        if (!this.value.trim()) {
            suggestionsDiv.innerHTML = '';
            suggestionsDiv.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            const text = encodeURIComponent(this.value);
            const apiKey = '{{ env('GEOAPIFY_API_KEY') }}';

            fetch(`https://api.geoapify.com/v1/geocode/autocomplete?text=${text}&format=json&apiKey=${apiKey}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsDiv.innerHTML = '';

                    if (data.results && data.results.length > 0) {
                        suggestionsDiv.classList.remove('hidden');

                        data.results.forEach(result => {
                            const div = document.createElement('div');
                            div.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                            div.textContent = result.formatted;

                            div.addEventListener('click', () => {
                                addressInput.value = result.formatted;
                                suggestionsDiv.classList.add('hidden');

                                // Add a hidden input with the full address data
                                let hiddenInput = document.getElementById('fullAddressData');
                                if (!hiddenInput) {
                                    hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.id = 'fullAddressData';
                                    hiddenInput.name = 'full_address_data';
                                    addressInput.parentNode.appendChild(hiddenInput);
                                }
                                hiddenInput.value = JSON.stringify(result);

                                updatePrices();
                            });

                            suggestionsDiv.appendChild(div);
                        });
                    } else {
                        suggestionsDiv.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error fetching address suggestions:', error);
                });
        }, 300);
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!addressInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.add('hidden');
        }
    });

    // Initialize the form state
    const checkedDeliveryMethod = document.querySelector('input[name="delivery_method_id"]:checked');
    if (checkedDeliveryMethod) {
        handleDeliveryMethodChange(checkedDeliveryMethod);
    }
    updatePrices();
});

function updatePrices() {
    const weightInput = document.querySelector('input[name="weight_id"]:checked');
    const selectedDeliveryMethod = document.querySelector('input[name="delivery_method_id"]:checked');

    if (!weightInput || !selectedDeliveryMethod) {
        return;
    }

    const requiresLocation = selectedDeliveryMethod.dataset.requiresLocation === "1";

    // Get prices
    const weightPrice = weightInput.dataset.price || 0;
    const deliveryPrice = selectedDeliveryMethod.dataset.price || 0;

    // Update display
    document.getElementById('weightPrice').textContent = `€${Number(weightPrice).toFixed(2)}`;

    // Update hidden inputs for form submission
    document.querySelector('input[name="weight_price"]').value = weightPrice;

    // Prepare the data object
    const data = {
        weight_id: weightInput.value,
        delivery_method_id: selectedDeliveryMethod.value,
        weight_price: weightPrice,
        delivery_price: deliveryPrice
    };

    // Add location or address data based on delivery method
    if (requiresLocation) {
        const locationSelect = document.querySelector('select[name="destination_location_id"]');
        if (locationSelect && locationSelect.value) {
            data.destination_location_id = locationSelect.value;
            console.log("Sending location ID:", locationSelect.value);
        } else {
            console.log("No location selected yet");
            document.getElementById('deliveryPrice').textContent = `€${Number(deliveryPrice).toFixed(2)}`;
            document.getElementById('totalPrice').textContent = `€${(Number(weightPrice) + Number(deliveryPrice)).toFixed(2)}`;
            document.querySelector('input[name="delivery_price"]').value = deliveryPrice;
            return;
        }
    } else {
        const fullAddressData = document.getElementById('fullAddressData')?.value;
        if (fullAddressData) {
            data.address_data = JSON.parse(fullAddressData);
            console.log("Sending address data:", data.address_data);
        } else {
            console.log("No address data available yet");
            document.getElementById('deliveryPrice').textContent = `€${Number(deliveryPrice).toFixed(2)}`;
            document.getElementById('totalPrice').textContent = `€${(Number(weightPrice) + Number(deliveryPrice)).toFixed(2)}`;
            document.querySelector('input[name="delivery_price"]').value = deliveryPrice;
            return;
        }
    }

    console.log("Sending data to server:", data);

    document.getElementById("sendpackage").disabled = true;
    document.getElementById("totalPrice").textContent = "Calculating Price...";
    document.getElementById("loadingSpinner").classList.remove("hidden");

    // Make the AJAX call to the controller
    fetch('{{ route("update-prices") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            ...data,
            sender_country_name: document.querySelector('select[name="sender_country_name"]')?.value
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log("Received response:", data);
        if (data.updatedDeliveryPrice) {
            document.getElementById('deliveryPrice').textContent = "€" + Number(data.updatedDeliveryPrice).toFixed(2);
            document.querySelector('input[name="delivery_price"]').value = data.updatedDeliveryPrice;
        }
        if (data.updatedTotalPrice) {
            document.getElementById('totalPrice').textContent = "€" + Number(data.updatedTotalPrice).toFixed(2);
        }
    })
    .catch(error => {
        console.error('Error updating prices:', error);
    })
    .finally(() => {
        document.getElementById("loadingSpinner").classList.add("hidden");
        document.getElementById("sendpackage").disabled = false;
    });
}

function handleDeliveryMethodChange(radio) {
    const requiresLocation = radio.dataset.requiresLocation === "1";
    const deliveryCode = radio.dataset.code;
    const locationSection = document.getElementById('locationSection');
    const addressSection = document.getElementById('addressSection');
    const locationSelect = document.querySelector('select[name="destination_location_id"]');

    // Reset form values
    locationSelect.value = '';

    locationSection.style.display = 'none';
    addressSection.style.display = 'none';

    if (requiresLocation) {
        locationSection.style.display = 'block';

        locationSelect.querySelectorAll('optgroup').forEach(group => {
            group.style.display = 'none';
        });

        const matchingGroup = locationSelect.querySelector(`optgroup[data-code="${deliveryCode}"]`);
        if (matchingGroup) {
            matchingGroup.style.display = '';
        }
    } else {
        addressSection.style.display = 'block';
    }

    updatePrices();
}
        function togglePasswordFields() {
            const checkbox = document.getElementById('checked_on_create_account');
            const passwordFields = document.getElementById('passwordFields');
            passwordFields.style.display = checkbox.checked ? 'flex' : 'none';
        }
    </script>
</x-app-layout>
