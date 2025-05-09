<x-app-layout>
    @section("title","Invoice Payment System")
    <div class="bg-gray-100 min-h-screen py-10">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-extrabold mb-10 text-gray-800 text-center tracking-tight">Invoice Management System</h1>
            <div class="flex flex-col md:flex-row gap-8 md:gap-6 justify-center">

                <!-- Unpaid Invoices Column -->
                <div class="md:w-5/12 w-full bg-white rounded-2xl shadow-xl p-6">
                    <h2 class="text-xl font-bold mb-5 text-blue-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        Unpaid Invoices
                    </h2>
                    <!-- Invoice List -->
                    <div class="space-y-3">
                        @forelse($invoices as $invoice)
                        <a href="?invoice={{ $invoice->id }}">
                            <div class="border transition-all border-gray-200 p-4 rounded-lg shadow-sm
                                {{ request('invoice') == $invoice->id ? 'bg-blue-50 border-blue-500' : 'hover:bg-blue-100/60' }} cursor-pointer">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">Invoice #{{ $invoice->reference }}</h3>
                                        <p class="text-xs text-gray-500">Due: {{ \Carbon\Carbon::parse($invoice->expiry_date)->format('Y-m-d') }}</p>
                                        <p class="text-xs text-gray-500">Company: {{ $invoice->company->company_name }}</p>
                                    </div>
                                    <div class="h-4 w-4 rounded-full {{ request('invoice') == $invoice->id ? 'bg-blue-500' : 'bg-blue-200' }}"></div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="text-gray-400 text-center py-8">No unpaid invoices found.</div>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        {{$invoices->appends(request()->query())->links()}}
                    </div>
                </div>

                <!-- Mark as Paid Button Section -->
                <form action="{{ route('invoices.mark-as-paid') }}" method="POST" class="md:w-2/12 w-full flex flex-col items-center justify-center gap-4 md:gap-0">
                    @csrf
                    <input type="hidden" name="invoice" value="{{ request()->query('invoice') }}">
                    <button
                        type="submit"
                        class="w-full bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 shadow-lg flex items-center justify-center gap-2 font-semibold text-lg transition transition-colors duration-150
                        {{ !request('invoice') ? 'opacity-50 pointer-events-none' : '' }}">
                        <span>Mark as Paid</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <span class="text-gray-400 text-sm mt-2">{{ request('invoice') ? 'Selected Invoice: #' . request('invoice') : 'Select an invoice' }}</span>
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
                                <div class="h-4 w-4 rounded-full bg-green-400"></div>
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
</x-app-layout>
