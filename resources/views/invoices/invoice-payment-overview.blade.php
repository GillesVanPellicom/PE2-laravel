
<x-app-layout>
    @section("title","Invoice Payment System")
    <div class="bg-gray-100 min-h-screen py-10">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-extrabold mb-10 text-gray-800 text-center tracking-tight">Invoice Management System</h1>
            <!-- Hidden GET form for selection -->
            <form id="invoice-select-form" method="GET" action="">
                <div id="invoice-select-inputs">
                    @foreach($selectedInvoices ?? [] as $invId)
                        <input type="hidden" name="invoices[]" value="{{ $invId }}">
                    @endforeach
                </div>
            </form>
            <div class="flex flex-col md:flex-row gap-8 md:gap-6 justify-center">

                <!-- Unpaid Invoices Column -->
                <div class="md:w-5/12 w-full bg-white rounded-2xl shadow-xl p-6">
                    <h2 class="text-xl font-bold mb-5 text-blue-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        Unpaid Invoices
                    </h2>
                    <div id="invoice-list" class="space-y-3">
                        @forelse($invoices as $invoice)
                        <div class="invoice-card flex items-center gap-3 border transition-all border-gray-200 p-4 rounded-lg shadow-sm cursor-pointer hover:bg-blue-100/60 {{ in_array($invoice->id, $selectedInvoices ?? []) ? 'bg-blue-50 border-blue-500' : '' }}"
                             data-invoice-id="{{ $invoice->id }}">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">Invoice #{{ $invoice->reference }}</h3>
                                <p class="text-xs text-gray-500">Due: {{ \Carbon\Carbon::parse($invoice->expiry_date)->format('Y-m-d') }}</p>
                                <p class="text-xs text-gray-500">Company: {{ $invoice->company->company_name }}</p>
                            </div>
                            <div class="h-4 w-4 rounded-full {{ in_array($invoice->id, $selectedInvoices ?? []) ? 'bg-blue-500' : 'bg-blue-200' }}"></div>
                        </div>
                        @empty
                        <div class="text-gray-400 text-center py-8">No unpaid invoices found.</div>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        {{$invoices->appends(request()->query())->links()}}
                    </div>
                </div>

                <!-- Mark as Paid Button Section -->
                <form id="mark-paid-form" action="{{ route('invoices.mark-as-paid') }}" method="POST" class="md:w-2/12 w-full flex flex-col items-center justify-center gap-4 md:gap-0">
                    @csrf
                    <div id="selected-invoice-inputs">
                        @foreach($selectedInvoices ?? [] as $invId)
                            <input type="hidden" name="invoices[]" value="{{ $invId }}">
                        @endforeach
                    </div>
                    <button
                        type="submit"
                        id="mark-paid-btn"
                        class="w-full bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 shadow-lg flex items-center justify-center gap-2 font-semibold text-lg transition transition-colors duration-150
                        {{ empty($selectedInvoices) ? 'opacity-50 pointer-events-none' : '' }}">
                        <span>Mark as Paid</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <span class="text-gray-400 text-sm mt-2" id="selected-invoices-label">
                        @if(!empty($selectedInvoices))
                            Selected: {{ implode(', ', $selectedInvoices) }}
                        @else
                            Select invoices
                        @endif
                    </span>
                </form>

                <!-- Matching Payments Column -->
                <div class="md:w-5/12 w-full bg-white rounded-2xl shadow-xl p-6">
                    <h2 class="text-xl font-bold mb-5 text-green-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Matching Payments
                    </h2>
                    <div class="space-y-3">
                        @forelse($payments as $payment)
                        <div class="border border-gray-200 p-4 rounded-lg bg-green-50 shadow-sm">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="font-semibold text-gray-800">Payment #{{ $payment->reference }}</h3>
                                    <p class="text-sm text-gray-600">{{ number_format($payment->amount, 2) }} €</p>
                                    <p class="text-xs text-gray-500">Date: {{ \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d H:i') }}</p>
                                    <p class="text-xs text-green-700 font-mono">Ref: {{ $payment->reference }}</p>
                                </div>
                                <!-- Dropdown Button -->
                                <button type="button" class="toggle-packages bg-green-200 hover:bg-green-300 rounded-full p-2 focus:outline-none" data-payment="{{ $payment->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>
                            <div class="packages-dropdown mt-2 hidden" id="packages-{{ $payment->id }}">
                                @if($payment->packages && count($payment->packages))
                                    <div class="bg-white rounded-lg shadow p-3">
                                        <h4 class="font-semibold text-gray-700 mb-2">Packages:</h4>
                                        <div class="list-disc pl-5 space-y-3">
                                            @foreach($payment->packages as $package)
                                                <div class="mb-4">
                                                    <div class="rounded-lg border border-yellow-900 bg-yellow-50/80 shadow-md p-4">
                                                        <div class="font-mono text-xs text-yellow-900 mb-1 font-bold">#{{ $package->reference }}</div>
                                                        <div class="text-yellow-900 text-sm mb-1">
                                                            <span class="font-semibold">Description:</span>
                                                            {{ $package->description ?? 'No description' }}
                                                        </div>
                                                        <div class="text-yellow-900 text-sm mb-1">
                                                            <span class="font-semibold">Origin:</span>
                                                            @php
                                                                $origin = $package->originLocation->address ?? null;
                                                            @endphp
                                                            {{ $origin ? ($origin->street . ' ' . $origin->house_number . ($origin->bus_number ? ' bus ' . $origin->bus_number : '')) : '-' }}
                                                        </div>
                                                        <div class="text-yellow-900 text-sm mb-1">
                                                            <span class="font-semibold">Destination:</span>
                                                            @php
                                                                $dest = $package->destinationLocation->address ?? null;
                                                            @endphp
                                                            {{ $dest ? ($dest->street . ' ' . $dest->house_number . ($dest->bus_number ? ' bus ' . $dest->bus_number : '')) : '-' }}
                                                        </div>
                                                        <div class="text-yellow-900 text-sm">
                                                            <span class="font-semibold">Receiver:</span>
                                                            {{ $package->name ?? '-' }} {{ $package->lastName ?? '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="text-gray-400 text-sm">No packages found for this payment.</div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-gray-400 text-center py-8">No matching payments found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // --- Invoice Card Selection with auto-submit ---
        let selectedInvoices = @json($selectedInvoices ?? []);
        const invoiceCards = document.querySelectorAll('.invoice-card');
        const selectInputsDiv = document.getElementById('invoice-select-inputs');
        const selectForm = document.getElementById('invoice-select-form');

        invoiceCards.forEach(card => {
            card.addEventListener('click', function() {
                const id = this.getAttribute('data-invoice-id');
                const idx = selectedInvoices.indexOf(id);
                if (idx === -1 && selectedInvoices.indexOf(Number(id)) === -1) {
                    selectedInvoices.push(id);
                } else {
                    selectedInvoices = selectedInvoices.filter(x => x != id && x != Number(id));
                }
                // Update hidden inputs and submit the form
                selectInputsDiv.innerHTML = '';
                selectedInvoices.forEach(id => {
                    selectInputsDiv.innerHTML += `<input type="hidden" name="invoices[]" value="${id}">`;
                });
                selectForm.submit();
            });
        });

        // --- Toggle package dropdowns ---
        document.querySelectorAll('.toggle-packages').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const id = this.getAttribute('data-payment');
                const dropdown = document.getElementById('packages-' + id);
                if (dropdown) dropdown.classList.toggle('hidden');
            });
        });
    </script>
</x-app-layout>