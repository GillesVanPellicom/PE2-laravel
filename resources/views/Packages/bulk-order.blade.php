<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-12">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200">
                    <h2 class="text-3xl font-bold text-gray-800">Bulk Order</h2>
                    <p class="mt-2 text-gray-600">Fill in the details to send multiple packages</p>
                </div>

                @if($errors->any())
                    <div class="px-8 py-4 bg-red-50">
                        <div class="flex items-center">
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
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('packages.bulk-order.store') }}" method="POST" class="px-8 py-6">
                    @csrf
                    <div id="packagesContainer" class="space-y-6">
                        <!-- Package Template -->
                        <div class="package bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Package Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="packages[0][name]" placeholder="Receiver Name" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="packages[0][lastName]" placeholder="Receiver Last Name" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="email" name="packages[0][receiverEmail]" placeholder="Receiver Email" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="packages[0][receiver_phone_number]" placeholder="Receiver Phone Number" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="packages[0][dimension]" placeholder="Dimension" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Weight Class Selection -->
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Weight Class</h4>
                                <div class="space-y-3">
                                    @foreach($weightClasses as $weightClass)
                                        <div class="flex items-center">
                                            <input type="radio" name="packages[0][weight_id]" value="{{ $weightClass->id }}"
                                                data-price="{{ $weightClass->price }}"
                                                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <label class="ml-3 text-sm text-gray-700">
                                                {{ $weightClass->name }} ({{ $weightClass->weight_min }} - {{ $weightClass->weight_max }} kg) - €{{ number_format($weightClass->price, 2) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Delivery Method Selection -->
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Delivery Method</h4>
                                <div class="space-y-3">
                                    @foreach($deliveryMethods as $method)
                                        <div class="flex items-center">
                                            <input type="radio" name="packages[0][delivery_method_id]" value="{{ $method->id }}"
                                                data-requires-location="{{ $method->requires_location }}"
                                                data-code="{{ $method->code }}"
                                                data-price="{{ $method->price }}"
                                                onchange="handleDeliveryMethodChange(this, 0)"
                                                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <label class="ml-3 text-sm text-gray-700">
                                                {{ $method->name }} - €{{ number_format($method->price, 2) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Dynamic Location Section -->
                            <div id="locationSection-0" style="display: none;" class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Select a Location</h3>
                                <select name="packages[0][destination_location_id]" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select a location</option>
                                    @foreach($deliveryMethods as $method)
                                        <optgroup label="{{ $method->name }}" data-code="{{ $method->code }}" style="display: none;">
                                            @foreach($locations->where('location_type', $method->code) as $location)
                                                <option value="{{ $location->id }}">
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
                            <div id="addressSection-0" style="display: none;" class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Address</h3>
                                <div class="w-full">
                                    <div class="relative"> 
                                        <input 
                                            id="addressInput-0" 
                                            type="text" 
                                            name="packages[0][addressInput]" 
                                            placeholder="Enter your address" 
                                            autocomplete="off"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        >
                                        <div id="suggestions-0" 
                                            class="absolute w-full top-full left-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg z-50 hidden max-h-60 overflow-y-auto">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Package Button -->
                    <div class="mt-6">
                        <button type="button" id="addPackageButton" 
                            class="px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                            Add Another Package
                        </button>
                    </div>
                        <div class="bg-gray-50 rounded-lg p-6 mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Total Price Summary</h3>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-900 font-medium">Total Price:</span>
                            <span id="totalPriceSummary" class="text-gray-900 font-medium">€0.00</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-6">
                        <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Submit Bulk Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let packageIndex = 1;

        document.getElementById('addPackageButton').addEventListener('click', function () {
        const container = document.getElementById('packagesContainer');
        const lastPackage = container.lastElementChild;

        // Clone the last package
        const newPackage = lastPackage.cloneNode(true);

        // Update input names and IDs for the new package
        Array.from(newPackage.querySelectorAll('input, select, div')).forEach(element => {
            const name = element.getAttribute('name');
            const id = element.getAttribute('id');

            if (name) {
                element.setAttribute('name', name.replace(/\d+/, packageIndex));
            }

            if (id) {
                element.setAttribute('id', id.replace(/\d+/, packageIndex));
            }
        });

        // Append the cloned package to the container
        container.appendChild(newPackage);
        packageIndex++;

        // Re-initialize autocomplete for the new package
        initializeAutocomplete(packageIndex - 1);
        
        // Reattach event listeners for the new radio buttons (delivery method & weight class)
        attachRadioEventListeners(packageIndex - 1);
        
        // Recalculate prices after adding a new package
        updatePrices();

        // Ensure that the location section is visible if the delivery method requires it
        const deliveryMethodRadio = newPackage.querySelector('input[name="packages[' + (packageIndex - 1) + '][delivery_method_id]"]:checked');
        if (deliveryMethodRadio) {
            handleDeliveryMethodChange(deliveryMethodRadio, packageIndex - 1);
        }
    });

    function handleDeliveryMethodChange(radio, index) {
        const requiresLocation = radio.dataset.requiresLocation === "1";
        const deliveryCode = radio.dataset.code;
        const locationSection = document.getElementById(`locationSection-${index}`);
        const addressSection = document.getElementById(`addressSection-${index}`);
        const locationSelect = document.querySelector(`select[name="packages[${index}][destination_location_id]"]`);

        // Reset form values
        locationSelect.value = '';

        // Hide both sections first
        locationSection.style.display = 'none';
        addressSection.style.display = 'none';

        if (requiresLocation) {
            locationSection.style.display = 'block';

            // Hide all optgroups first
            locationSelect.querySelectorAll('optgroup').forEach(group => {
                group.style.display = 'none';
            });

            // Show the matching optgroup
            const matchingGroup = locationSelect.querySelector(`optgroup[data-code="${deliveryCode}"]`);
            if (matchingGroup) {
                matchingGroup.style.display = '';
            }
        } else {
            addressSection.style.display = 'block';
        }
    }

        function initializeAutocomplete(index) {
            const addressInput = document.getElementById(`addressInput-${index}`);
            const suggestionsDiv = document.getElementById(`suggestions-${index}`);
            let debounceTimer;

            addressInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);

                // Clear suggestions if input is empty
                if (!this.value.trim()) {
                    suggestionsDiv.innerHTML = '';
                    suggestionsDiv.classList.add('hidden');
                    return;
                }

                // Debounce the API call
                debounceTimer = setTimeout(() => {
                    const text = encodeURIComponent(this.value);
                    const apiKey = '{{ env('GEOAPIFY_API_KEY') }}';

                    fetch(`https://api.geoapify.com/v1/geocode/autocomplete?text=${text}&format=json&apiKey=${apiKey}`)
                        .then(response => response.json())
                        .then(data => {
                            // Clear previous suggestions
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
        }

        function updatePrices() {
            let totalPrice = 0;

            // Loop through all packages to calculate total price
            for (let i = 0; i < packageIndex; i++) {
                const weightInput = document.querySelector(`input[name="packages[${i}][weight_id]"]:checked`);
                const deliveryInput = document.querySelector(`input[name="packages[${i}][delivery_method_id]"]:checked`);

                const weightPrice = weightInput ? Number(weightInput.dataset.price) : 0;
                const deliveryPrice = deliveryInput ? Number(deliveryInput.dataset.price) : 0;

                totalPrice += weightPrice + deliveryPrice;
            }

            // Update the total price displayed
            document.getElementById('totalPriceSummary').textContent = `€${totalPrice.toFixed(2)}`;
        }

        // Attach event listeners for the new package's radio buttons
        function attachRadioEventListeners(packageIndex) {
            const weightRadios = document.querySelectorAll(`input[name="packages[${packageIndex}][weight_id]"]`);
            const deliveryRadios = document.querySelectorAll(`input[name="packages[${packageIndex}][delivery_method_id]"]`);

            // Attach event listeners to weight radio buttons
            weightRadios.forEach(radio => {
                radio.addEventListener('change', updatePrices);
            });

            // Attach event listeners to delivery method radio buttons
            deliveryRadios.forEach(radio => {
                radio.addEventListener('change', updatePrices);
            });
        }

        // Trigger price update on radio button change (for both weight and delivery method)
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', updatePrices);
        });

        // Call updatePrices when page loads to account for any pre-selected options
        document.addEventListener('DOMContentLoaded', updatePrices);


        function handleDeliveryMethodChange(radio, index) {
            const requiresLocation = radio.dataset.requiresLocation === "1";
            const deliveryCode = radio.dataset.code;
            const locationSection = document.getElementById(`locationSection-${index}`);
            const addressSection = document.getElementById(`addressSection-${index}`);
            const locationSelect = document.querySelector(`select[name="packages[${index}][destination_location_id]"]`);

            // Reset form values
            locationSelect.value = '';

            // Hide both sections first
            locationSection.style.display = 'none';
            addressSection.style.display = 'none';

            if (requiresLocation) {
                locationSection.style.display = 'block';

                // Hide all optgroups first
                locationSelect.querySelectorAll('optgroup').forEach(group => {
                    group.style.display = 'none';
                });

                // Show the matching optgroup
                const matchingGroup = locationSelect.querySelector(`optgroup[data-code="${deliveryCode}"]`);
                if (matchingGroup) {
                    matchingGroup.style.display = '';
                }
            } else {
                addressSection.style.display = 'block';
            }
        }

        initializeAutocomplete(0);
    </script>
</x-app-layout>