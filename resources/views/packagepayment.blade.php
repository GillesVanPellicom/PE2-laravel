
<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
        <div class="relative py-3 sm:max-w-xl sm:mx-auto">
            <div class="relative px-4 py-10 bg-white mx-8 md:mx-0 shadow rounded-3xl sm:p-10">
                <div class="max-w-md mx-auto">
                    <div class="flex items-center space-x-5">
                        <div class="block pl-2 font-semibold text-xl text-gray-700">
                            <h2 class="leading-relaxed">Payment Details</h2>
                            <p class="text-sm text-gray-500 font-normal">Complete your payment securely</p>
                        </div>
                    </div>

                    @php
                        $isBulk = session('bulk_order_package_ids') && count(session('bulk_order_package_ids')) > 0;
                        $packageId = $isBulk ? session('bulk_order_package_ids')[0] : $package->id;
                        $routeName = $isBulk ? 'packages.bulk-details' : 'packages.packagedetails';
                    @endphp

                    <form id="submit-form" 
                        action="{{ route($routeName, ['id' => $isBulk ? implode(',', session('bulk_order_package_ids')) : $package->id]) }}" 
                        method="GET" 
                        class="mt-8 space-y-6">
                        @csrf
                        <div class="bg-white">
                            <!-- Amount Display -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-medium text-gray-900">Total Amount:</span>
                                    <span class="text-2xl font-bold text-blue-600">
                                    @if(session('bulk_order_total_price'))
                                        €{{ number_format(session('bulk_order_weight_price', 0) + session('bulk_order_delivery_price', 0), 2) }}
                                    @else
                                    €{{ number_format(($package->weight_price ?? 0) + ($package->delivery_price ?? 0), 2) }}
                                    @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Payment Method Selection -->
                            <div class="mb-6">
                                <label class="text-sm font-medium text-gray-700">Select Payment Method</label>
                                <div class="mt-3 grid grid-cols-3 gap-3">
                                    <!-- Credit Card -->
                                    <div>
                                        <input type="radio" name="payment_method" id="credit_card" value="credit_card"
                                               class="hidden peer" checked>
                                        <label for="credit_card"
                                               class="flex flex-col items-center justify-center p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-50">
                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                                </path>
                                            </svg>
                                            <span class="text-sm font-medium">Credit Card</span>
                                        </label>
                                    </div>

                                    <!-- PayPal -->
                                    <div>
                                        <input type="radio" name="payment_method" id="paypal" value="paypal"
                                               class="hidden peer">
                                        <label for="paypal"
                                               class="flex flex-col items-center justify-center p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-50">
                                            <svg class="w-6 h-6 mb-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944 3.217a.641.641 0 0 1 .632-.544h7.168c3.738 0 6.415 2.345 6.415 5.629 0 4.094-3.829 7.118-8.451 7.118h-2.97l-1.048 5.917a.641.641 0 0 1-.633.544z">
                                                </path>
                                            </svg>
                                            <span class="text-sm font-medium">PayPal</span>
                                        </label>
                                    </div>

                                    <!-- Bancontact (renamed from Bank Transfer) -->
                                    <div>
                                        <input type="radio" name="payment_method" id="bancontact" value="bancontact"
                                               class="hidden peer">
                                        <label for="bancontact"
                                               class="flex flex-col items-center justify-center p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-50">
                                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <span class="text-sm font-medium">Bancontact</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Credit Card Form -->
                            <div id="credit_card_form" class="space-y-4">
                                <div>
                                    <label for="card_number" id="label_card_number" class="block text-sm font-medium text-gray-700 transition-all">Card Number</label>
                                    <input type="text" name="card_number" id="card_number"
                                           placeholder="1234 5678 9012 3456"
                                           maxlength="23"
                                           autocomplete="cc-number"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all">
                                    <span class="text-red-600 text-xs font-semibold" id="card_number_error"></span>
                                    @error('card_number')
                                        <span class="text-red-600 text-xs font-semibold">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="expiry_date" id="label_expiry_date" class="block text-sm font-medium text-gray-700 transition-all">Expiry Date</label>
                                        <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YY"
                                               maxlength="5"
                                               autocomplete="cc-exp"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all">
                                        <span class="text-red-600 text-xs font-semibold" id="expiry_date_error"></span>
                                        @error('expiry_date')
                                            <span class="text-red-600 text-xs font-semibold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="cvv" id="label_cvv" class="block text-sm font-medium text-gray-700 transition-all">CVV</label>
                                        <input type="text" name="cvv" id="cvv" placeholder="123"
                                               maxlength="3"
                                               autocomplete="cc-csc"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all">
                                        <span class="text-red-600 text-xs font-semibold" id="cvv_error"></span>
                                        @error('cvv')
                                            <span class="text-red-600 text-xs font-semibold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="card_name" id="label_card_name" class="block text-sm font-medium text-gray-700 transition-all">Name on Card</label>
                                    <input type="text" name="card_name" id="card_name"
                                           autocomplete="cc-name"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all">
                                    <span class="text-red-600 text-xs font-semibold" id="card_name_error"></span>
                                    @error('card_name')
                                        <span class="text-red-600 text-xs font-semibold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Bancontact Form (same as Credit Card, but no CVV) -->
                            <div id="bancontact_form" class="space-y-4 hidden">
                                <div>
                                    <label for="bancontact_card_number" id="label_bancontact_card_number" class="block text-sm font-medium text-gray-700 transition-all">
                                        Card Number
                                    </label>
                                    <input type="text" name="bancontact_card_number" id="bancontact_card_number"
                                           placeholder="1234 5678 9012 3456"
                                           maxlength="23"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all">
                                    <span class="text-red-600 text-xs font-semibold" id="bancontact_card_number_error"></span>
                                    @error('bancontact_card_number')
                                        <span class="text-red-600 text-xs font-semibold">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="bancontact_expiry_date" id="label_bancontact_expiry_date" class="block text-sm font-medium text-gray-700 transition-all">
                                        Expiry Date
                                    </label>
                                    <input type="text" name="bancontact_expiry_date" id="bancontact_expiry_date"
                                           placeholder="MM/YY"
                                           maxlength="5"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all">
                                    <span class="text-red-600 text-xs font-semibold" id="bancontact_expiry_date_error"></span>
                                    @error('bancontact_expiry_date')
                                        <span class="text-red-600 text-xs font-semibold">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="bancontact_card_name" id="label_bancontact_card_name" class="block text-sm font-medium text-gray-700 transition-all">
                                        Name on Card
                                    </label>
                                    <input type="text" name="bancontact_card_name" id="bancontact_card_name"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all">
                                    <span class="text-red-600 text-xs font-semibold" id="bancontact_card_name_error"></span>
                                    @error('bancontact_card_name')
                                        <span class="text-red-600 text-xs font-semibold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- PayPal Section -->
                            <div id="paypal_form" class="hidden">
                                <div class="bg-gray-50 p-4 rounded-lg text-center">
                                    <p class="text-sm text-gray-600">
                                        You will be redirected to PayPal to complete your payment
                                    </p>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-6">
                                <button type="submit"
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Complete Payment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Grab form sections
        const creditCardForm = document.getElementById("credit_card_form");
        const bancontactForm = document.getElementById("bancontact_form");
        const paypalForm = document.getElementById("paypal_form");

        // Toggle visible form based on selected payment method
        document.querySelectorAll('input[name="payment_method"]').forEach((elem) => {
            elem.addEventListener("change", function(event) {
                creditCardForm.classList.add("hidden");
                bancontactForm.classList.add("hidden");
                paypalForm.classList.add("hidden");

                switch(event.target.value) {
                    case "credit_card":
                        creditCardForm.classList.remove("hidden");
                        break;
                    case "bancontact":
                        bancontactForm.classList.remove("hidden");
                        break;
                    case "paypal":
                        paypalForm.classList.remove("hidden");
                        break;
                }
            });
        });

        // Auto-format card number (spaces every 4 digits)
        function formatCardNumber(value) {
            return value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
        }
        function handleCardNumberInput(e) {
            const formatted = formatCardNumber(e.target.value);
            e.target.value = formatted;
        }
        document.getElementById('card_number').addEventListener('input', handleCardNumberInput);
        document.getElementById('bancontact_card_number').addEventListener('input', handleCardNumberInput);

        // Auto-format expiry date (MM/YY)
        function formatExpiry(value) {
            let v = value.replace(/\D/g, '');
            if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2,4);
            return v.slice(0,5);
        }
        function handleExpiryInput(e) {
            e.target.value = formatExpiry(e.target.value);
        }
        document.getElementById('expiry_date').addEventListener('input', handleExpiryInput);
        document.getElementById('bancontact_expiry_date').addEventListener('input', handleExpiryInput);

        // Helper to set error styles
        function setFieldError(field, hasError) {
            const input = document.getElementById(field);
            const label = document.getElementById('label_' + field);
            if (input && label) {
                if (hasError) {
                    input.classList.add('border-red-500', 'focus:border-red-600');
                    label.classList.add('text-red-600');
                } else {
                    input.classList.remove('border-red-500', 'focus:border-red-600');
                    label.classList.remove('text-red-600');
                }
            }
        }

        // Remove error on focus/input
        [
            'card_number', 'expiry_date', 'cvv', 'card_name',
            'bancontact_card_number', 'bancontact_expiry_date', 'bancontact_card_name'
        ].forEach(field => {
            const input = document.getElementById(field);
            if (input) {
                input.addEventListener('focus', () => {
                    setFieldError(field, false);
                    const err = document.getElementById(field + '_error');
                    if (err) err.textContent = '';
                });
                input.addEventListener('input', () => {
                    setFieldError(field, false);
                    const err = document.getElementById(field + '_error');
                    if (err) err.textContent = '';
                });
            }
        });

        // Client-side validation with error display under each field
        document.getElementById('submit-form').addEventListener('submit', function(event) {
            // Clear previous errors and styles
            [
                'card_number', 'expiry_date', 'cvv', 'card_name',
                'bancontact_card_number', 'bancontact_expiry_date', 'bancontact_card_name'
            ].forEach(field => setFieldError(field, false));
            [
                'card_number_error', 'expiry_date_error', 'cvv_error', 'card_name_error',
                'bancontact_card_number_error', 'bancontact_expiry_date_error', 'bancontact_card_name_error'
            ].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = '';
            });

            let valid = true;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            function showError(id, msg) {
                const el = document.getElementById(id);
                if (el) el.textContent = msg;
            }

            function validateCardName(name) {
                return /^[A-Za-z\s]{1,30}$/.test(name);
            }
            function validateCardNumber(num) {
                const digits = num.replace(/\s+/g, '');
                return /^\d{16,19}$/.test(digits);
            }
            function validateExpiry(dateStr) {
                const match = dateStr.match(/^(\d{2})\/(\d{2})$/);
                if (!match) return false;
                let month = parseInt(match[1], 10);
                let year = parseInt(match[2], 10);
                if (year < 25 || year > 30) return false;
                if (year === 25 && month < 5) return false; // Now valid from 05/25
                if (year === 30 && month > 1) return false;
                if (month < 1 || month > 12) return false;
                return true;
            }
            function validateCVV(cvv) {
                return /^\d{3}$/.test(cvv);
            }

            if (paymentMethod === 'credit_card') {
                const cardNumber = document.getElementById('card_number').value;
                const expiryDate = document.getElementById('expiry_date').value;
                const cvv = document.getElementById('cvv').value;
                const cardName = document.getElementById('card_name').value;

                if (!validateCardNumber(cardNumber)) {
                    valid = false;
                    showError('card_number_error', 'Credit Card number must be 16–19 digits (numbers only).');
                    setFieldError('card_number', true);
                }
                if (!validateExpiry(expiryDate)) {
                    valid = false;
                    showError('expiry_date_error', 'Expiry date must be MM/YY and valid from 05/25');
                    setFieldError('expiry_date', true);
                }
                if (!validateCVV(cvv)) {
                    valid = false;
                    showError('cvv_error', 'CVV must be exactly 3 digits.');
                    setFieldError('cvv', true);
                }
                if (!validateCardName(cardName)) {
                    valid = false;
                    showError('card_name_error', 'Name on Card must contain only letters (max 30 chars).');
                    setFieldError('card_name', true);
                }
            } else if (paymentMethod === 'bancontact') {
                const cardNumber = document.getElementById('bancontact_card_number').value;
                const expiryDate = document.getElementById('bancontact_expiry_date').value;
                const cardName = document.getElementById('bancontact_card_name').value;

                if (!validateCardNumber(cardNumber)) {
                    valid = false;
                    showError('bancontact_card_number_error', 'Bancontact Card number must be 16–19 digits (numbers only).');
                    setFieldError('bancontact_card_number', true);
                }
                if (!validateExpiry(expiryDate)) {
                    valid = false;
                    showError('bancontact_expiry_date_error', 'Expiry date must be MM/YY and valid from 05/25');
                    setFieldError('bancontact_expiry_date', true);
                }
                if (!validateCardName(cardName)) {
                    valid = false;
                    showError('bancontact_card_name_error', 'Name on Card must contain only letters (max 30 chars).');
                    setFieldError('bancontact_card_name', true);
                }
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    </script>
</x-app-layout>