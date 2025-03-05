<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Send a Parcel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-2xl bg-gray-50 rounded-lg shadow-lg p-8">  
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Send a Parcel</h2>
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif


        @if(session('completed'))
            <div class="text-center mt-8">
                <a href="{{ route('parcel.create') }}" class="bg-blue-500 text-white px-8 py-3 rounded hover:bg-blue-600 transition-colors">
                    Send Another Parcel
                </a>
            </div>
        @else
            <form id="parcel-form" action="{{ route('parcel.store') }}" method="POST" class="space-y-6" novalidate>
                @csrf
                <input type="hidden" name="delivery_method_price" id="delivery_method_price" value="0">
                <input type="hidden" name="weight_price" id="weight_price" value="0">
                <input type="hidden" name="current_step" id="current_step" value="{{ $currentStep }}">

                <div id="step1" class="{{ $currentStep === 1 ? 'block' : 'hidden' }}">
                    <x-parcel.step1 :countries="$countries" :deliveryMethods="$deliveryMethods" :weightClasses="$weightClasses" />
                </div>

                <div id="step2" class="{{ $currentStep === 2 ? 'block' : 'hidden' }}">
                    <x-parcel.step2 :deliveryMethod="old('delivery_method') ?? session('parcel_data.step1.delivery_method')" />
                </div>

                <div id="step3" class="{{ $currentStep === 3 ? 'block' : 'hidden' }}">
                    <x-parcel.step3 :countries="$countries" />
                </div>

                <div class="border rounded p-4 mt-6 bg-gray-50">
                    <h2 class="font-bold mb-4">Parcel overview</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Delivery method</span>
                            <span id="delivery-price">€ 0,00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Weight surcharge</span>
                            <span id="weight-price">€ 0,00</span>
                        </div>
                        <div class="border-t pt-2 mt-2">
                            <div class="flex justify-between font-bold">
                                <span>Total price</span>
                                <span id="total-price">€ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 border-t pt-8 flex justify-between">
                    <button type="button" onclick="previousStep()" class="px-8 py-3 rounded text-black font-medium bg-gray-50 hover:bg-gray-700 transition-colors {{ $currentStep === 1 ? 'hidden' : '' }}">
                        Previous
                    </button>
                    <button type="button" onclick="nextStep()" class="px-8 py-3 rounded text-black font-medium bg-blue-50 hover:bg-blue-600 transition-colors">
                        {{ $currentStep < 3 ? 'Next' : 'Submit' }}
                    </button>
                </div>
            </form>
        @endif
    </div>
    </div>

    <script>
        // Utility functions
        function updateNavigationButtons() {
            const prevButton = document.querySelector('[onclick="previousStep()"]');
            if (prevButton) {
                const currentStep = parseInt(document.getElementById('current_step').value);
                if (currentStep > 1) {
                    prevButton.classList.remove('hidden');
                } else {
                    prevButton.classList.add('hidden');
                }
            }
            
            const nextButton = document.querySelector('[onclick="nextStep()"]');
            if (nextButton) {
                const currentStep = parseInt(document.getElementById('current_step').value);
                nextButton.textContent = currentStep < 3 ? 'Next' : 'Submit';
            }
        }

        function validateStep1() {
            const deliveryMethod = document.querySelector('input[name="delivery_method"]:checked');
            const weightClass = document.querySelector('input[name="weight_class"]:checked');
            const country = document.querySelector('select[name="country"]').value;
            
            // Clear previous error messages
            document.querySelectorAll('.validation-error').forEach(el => el.remove());
            
            let isValid = true;
            
            if (!country) {
                addError('country', 'Please select a country');
                isValid = false;
            }
            
            if (!deliveryMethod) {
                addError('delivery_method', 'Please select a delivery method');
                isValid = false;
            }
            
            if (!weightClass) {
                addError('weight_class', 'Please select a weight class');
                isValid = false;
            }
            
            return isValid;
        }

        function addError(fieldName, message) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            const errorDiv = document.createElement('p');
            errorDiv.className = 'validation-error text-red-600 text-sm mt-1 font-medium';
            errorDiv.textContent = message;
            field.parentElement.appendChild(errorDiv);
        }

        function updatePrices() {
            const form = document.getElementById('parcel-form');
            const selectedDelivery = form.querySelector('input[name="delivery_method"]:checked');
            const selectedWeight = form.querySelector('input[name="weight_class"]:checked');

            const deliveryPrice = selectedDelivery ? parseFloat(selectedDelivery.dataset.price) : 0;
            const weightPrice = selectedWeight ? parseFloat(selectedWeight.dataset.price) : 0;
            const totalPrice = deliveryPrice + weightPrice;

            document.getElementById('delivery_method_price').value = deliveryPrice;
            document.getElementById('weight_price').value = weightPrice;
            document.getElementById('delivery-price').textContent = `€ ${deliveryPrice.toFixed(2)}`;
            document.getElementById('weight-price').textContent = `€ ${weightPrice.toFixed(2)}`;
            document.getElementById('total-price').textContent = `€ ${totalPrice.toFixed(2)}`;
        }

        // Navigation functions
        function previousStep() {
            const currentStepElement = document.getElementById('current_step');
            let currentStep = parseInt(currentStepElement.value);
            
            if (currentStep > 1) {
                document.querySelectorAll('#step1, #step2, #step3').forEach(step => {
                    step.classList.add('hidden');
                });
                currentStep--;
                document.getElementById('step' + currentStep).classList.remove('hidden');
                currentStepElement.value = currentStep;
                updateNavigationButtons();
            }
        }

        function nextStep() {
            const form = document.getElementById('parcel-form');
            const currentStepElement = document.getElementById('current_step');
            let currentStep = parseInt(currentStepElement.value);

            if (currentStep === 1 && !validateStep1()) {
                return;
            }

            const formData = new FormData(form);
            formData.set('current_step', currentStep);

            fetch('{{ route('parcel.store') }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (currentStep < 3) {
                        currentStep++;
                        currentStepElement.value = currentStep;
                        
                        document.querySelectorAll('#step1, #step2, #step3').forEach(step => {
                            step.classList.add('hidden');
                        });
                        
                        document.getElementById('step' + currentStep).classList.remove('hidden');
                        updateNavigationButtons();
                    } else {
                        const formContainer = document.querySelector('.max-w-2xl');
                        formContainer.innerHTML = `
                            <div class="bg-white p-6 rounded-lg shadow-lg">
                                <h2 class="text-2xl font-bold mb-4">Parcel Data Summary</h2>
                                <pre class="bg-gray-50 p-4 rounded overflow-auto">${JSON.stringify(data.data, null, 2)}</pre>
                                <div class="text-center mt-6">
                                    <a href="{{ route('parcel.create') }}" class="bg-blue-500 text-white px-8 py-3 rounded hover:bg-blue-600">
                                        Send Another Parcel
                                    </a>
                                </div>
                            </div>
                        `;
                    }
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const input = document.querySelector(`[name="${key}"]`);
                            if (input) {
                                const errorDiv = document.createElement('p');
                                errorDiv.className = 'text-red-600 text-sm mt-1';
                                errorDiv.textContent = data.errors[key][0];
                                input.parentElement.appendChild(errorDiv);
                            }
                        });
                    } else {
                        alert(data.message || 'An error occurred');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request');
            });
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('parcel-form');
            updateNavigationButtons();
            updatePrices();

            // Add event listeners to radio buttons for price updates
            form.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.addEventListener('change', updatePrices);
            });
        });
    </script>
</body>
</html> 