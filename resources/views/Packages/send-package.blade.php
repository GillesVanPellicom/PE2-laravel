<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-12">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200">
                    <h2 class="text-3xl font-bold text-gray-800">Send Package</h2>
                    <p class="mt-2 text-gray-600">Fill in the details to send your package</p>
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
                        <!-- Customer Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="customer_id" placeholder="Customer ID" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="origin_location_id" placeholder="Origin Location ID" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Receiver Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Receiver Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="name" placeholder="Name" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="lastName" placeholder="Last Name" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="email" name="receiverEmail" placeholder="Email" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="receiver_phone_number" placeholder="Phone Number" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Package Details -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Package Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="reference" placeholder="Reference" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="status" placeholder="Status" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="dimension" placeholder="Dimension" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Weight Class Selection -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Weight Class</h3>
                            <div class="space-y-3">
                                @foreach($weightClasses as $weightClass)
                                    <div class="flex items-center">
                                        <input type="radio" name="weight_id" value="{{ $weightClass->id }}"
                                            data-price="{{ $weightClass->price }}"
                                            onchange="updatePrices()"
                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <label class="ml-3 text-sm text-gray-700">
                                            {{ $weightClass->name }} ({{ $weightClass->weight_min }} - {{ $weightClass->weight_max }} kg) - €{{ number_format($weightClass->price, 2) }}
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
                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <label class="ml-3 text-sm text-gray-700">
                                            {{ $method->name }} - €{{ number_format($method->price, 2) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Dynamic Location Section -->
                        <div id="locationSection" style="display: none;" class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Select Location</h3>
                            <select name="destination_location_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select a location</option>
                                @foreach($deliveryMethods as $method)
                                    <optgroup label="{{ $method->name }}" data-code="{{ $method->code }}" style="display: none;">
                                        @foreach($locations->where('location_type', $method->code) as $location)
                                            <option value="{{ $location->id }}">
                                                {{ $location->name }} - {{ $location->address }}
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="street" placeholder="Street" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="house_number" placeholder="House Number" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="cities_id" placeholder="City ID" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <input type="text" name="country_id" placeholder="Country ID" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
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
                                    <div class="flex justify-between">
                                        <span class="text-gray-900 font-medium">Total Price:</span>
                                        <span id="totalPrice" class="text-gray-900 font-medium">€0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
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
        function updatePrices() {
            const weightInput = document.querySelector('input[name="weight_id"]:checked');
            const selectedDeliveryMethod = document.querySelector('input[name="delivery_method_id"]:checked');
            
            // Get prices
            const weightPrice = weightInput?.dataset?.price || 0;
            const deliveryPrice = selectedDeliveryMethod?.dataset?.price || 0;
            
            // Update display
            document.getElementById('weightPrice').textContent = `€${Number(weightPrice).toFixed(2)}`;
            document.getElementById('deliveryPrice').textContent = `€${Number(deliveryPrice).toFixed(2)}`;
            document.getElementById('totalPrice').textContent = `€${(Number(weightPrice) + Number(deliveryPrice)).toFixed(2)}`;
            
            // Update hidden inputs for form submission
            document.querySelector('input[name="weight_price"]').value = weightPrice;
            document.querySelector('input[name="delivery_price"]').value = deliveryPrice;
        }

        function handleDeliveryMethodChange(radio) {
            const requiresLocation = radio.dataset.requiresLocation === "1";
            const deliveryCode = radio.dataset.code;
            const locationSection = document.getElementById('locationSection');
            const addressSection = document.getElementById('addressSection');
            const locationSelect = document.querySelector('select[name="destination_location_id"]');
            
            // Reset form values
            locationSelect.value = '';
            document.querySelector('input[name="street"]').value = '';
            document.querySelector('input[name="house_number"]').value = '';
            document.querySelector('input[name="cities_id"]').value = '';
            document.querySelector('input[name="country_id"]').value = '';

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
            
            // Update prices when delivery method changes
            updatePrices();
        }

        // Add form validation before submit
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
    </script>
</x-app-layout>