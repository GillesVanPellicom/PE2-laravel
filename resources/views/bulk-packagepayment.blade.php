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

                    <form id="submit-form" 
                        action="{{ route('packages.bulk-details', ['ids' => implode(',', session('bulk_order_package_ids', []))]) }}"
                        method="POST" 
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
                                    <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number</label>
                                    <input type="text" name="card_number" id="card_number"
                                           placeholder="1234 5678 9012 3456"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                        <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YY"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label for="cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                                        <input type="text" name="cvv" id="cvv" placeholder="123"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label for="card_name" class="block text-sm font-medium text-gray-700">Name on Card</label>
                                    <input type="text" name="card_name" id="card_name"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                            </div>

                            <!-- Bancontact Form (same as Credit Card, but no CVV) -->
                            <div id="bancontact_form" class="space-y-4 hidden">
                                <div>
                                    <label for="bancontact_card_number" class="block text-sm font-medium text-gray-700">
                                        Card Number
                                    </label>
                                    <input type="text" name="bancontact_card_number" id="bancontact_card_number"
                                           placeholder="1234 5678 9012 3456"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="bancontact_expiry_date" class="block text-sm font-medium text-gray-700">
                                        Expiry Date
                                    </label>
                                    <input type="text" name="bancontact_expiry_date" id="bancontact_expiry_date"
                                           placeholder="MM/YY"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="bancontact_card_name" class="block text-sm font-medium text-gray-700">
                                        Name on Card
                                    </label>
                                    <input type="text" name="bancontact_card_name" id="bancontact_card_name"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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
                                        id="submit-form"
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

        // Front-end validation on form submission
        document.getElementById('submit-form').addEventListener('submit', function(event) {
            let valid = true;
            let messages = [];
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            // Helper to validate card name (letters + spaces, up to 30 chars)
            function validateCardName(name) {
                return /^[A-Za-z\s]{1,30}$/.test(name);
            }

            // Helper to validate card number (16-19 digits)
            function validateCardNumber(num) {
                return /^\d{16,19}$/.test(num.replace(/\s+/g, ''));
            }

            // Helper to validate expiry date "MM/YY" from 04/25 to 01/30
            function validateExpiry(dateStr) {
                const match = dateStr.match(/^(\d{2})\/(\d{2})$/);
                if (!match) return false;
                let month = parseInt(match[1], 10);
                let year = parseInt(match[2], 10);

                // Must be between 25 and 30 inclusive
                if (year < 25 || year > 30) return false;

                // If year = 25, month >= 4
                if (year === 25 && month < 4) return false;

                // If year = 30, month <= 1
                if (year === 30 && month > 1) return false;

                // Month must be 1..12
                if (month < 1 || month > 12) return false;

                return true;
            }

            // Helper to validate CVV 
            function validateCVV(cvv) {
                return /^\d{3}$/.test(cvv);
            }

            // Validate if Credit Card or Bancontact is selected
            if (paymentMethod === 'credit_card') {
                const cardNumber = document.getElementById('card_number').value;
                const expiryDate = document.getElementById('expiry_date').value;
                const cvv = document.getElementById('cvv').value;
                const cardName = document.getElementById('card_name').value;

                if (!validateCardNumber(cardNumber)) {
                    valid = false;
                    messages.push('Credit Card number must be 16–19 digits (numbers only).');
                }
                if (!validateExpiry(expiryDate)) {
                    valid = false;
                    messages.push('Expiry date must be MM/YY and valid from 04/25 up to 01/30.');
                }
                if (!validateCVV(cvv)) {
                    valid = false;
                    messages.push('CVV must be exactly 3 digits.');
                }
                if (!validateCardName(cardName)) {
                    valid = false;
                    messages.push('Name on Card must contain only letters (max 30 chars).');
                }
            } else if (paymentMethod === 'bancontact') {
                const cardNumber = document.getElementById('bancontact_card_number').value;
                const expiryDate = document.getElementById('bancontact_expiry_date').value;
                const cardName = document.getElementById('bancontact_card_name').value;

                if (!validateCardNumber(cardNumber)) {
                    valid = false;
                    messages.push('Bancontact Card number must be 16–19 digits (numbers only).');
                }
                if (!validateExpiry(expiryDate)) {
                    valid = false;
                    messages.push('Expiry date must be MM/YY and valid from 04/25 up to 01/30.');
                }
                if (!validateCardName(cardName)) {
                    valid = false;
                    messages.push('Name on Card must contain only letters (max 30 chars).');
                }
            }

            if (!valid) {
                event.preventDefault();
                alert(messages.join('\n'));
            }
        });
    </script>
</x-app-layout>


