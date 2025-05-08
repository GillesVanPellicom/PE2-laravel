<x-app-layout>
    @section("title","Invoice Payment System")
<body class="bg-gray-100 p-6">
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Invoice Management System</h1>

        <div class="flex gap-4">
            <!-- Unpaid Invoices Column -->
            <div class="w-5/12 bg-white rounded-lg shadow-md p-4">
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Unpaid Invoices</h2>

                <!-- Invoice List -->
                <div class="space-y-3">
                    <!-- Invoice Item -->
                    @foreach($invoices as $invoice)
    <a href="?invoice={{ $invoice->id }}">
        <div class="border border-gray-200 p-3 rounded-md hover:bg-gray-50 cursor-pointer transition-colors">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-medium">Invoice #{{ $invoice->reference }}</h3>
                    <p class="text-xs text-gray-500">Due: {{ \Carbon\Carbon::parse($invoice->expiry_date)->format('Y-m-d') }}</p>
                    <p class="text-xs text-gray-500">Company: {{ $invoice->company->company_name }}</p>
                </div>
                <div class="h-4 w-4 rounded-full bg-blue-500"></div>
            </div>
        </div>
    </a>
@endforeach
                    {{$invoices->appends(request()->query())->links()}}

                </div>
            </div>

            <!-- Middle Section with Button -->
            <form action="{{ route('invoices.mark-as-paid') }}" method="POST" class="w-2/12 flex items-center justify-center">
                @csrf

                <input type="hidden" name="invoice" value="{{ request()->query('invoice') }}">

                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors shadow-md flex items-center space-x-2">
                    <span>Mark as Paid</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </form>


            <!-- Matching Payments Column -->
            <div class="w-5/12 bg-white rounded-lg shadow-md p-4">
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Matching Payments</h2>

                <!-- Payments List -->
                <div class="space-y-3">
                    <!-- Payment Item -->
                    @foreach($payments as $payment)
                    <div class="border border-gray-200 p-3 rounded-md bg-gray-50">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-medium">Payment{{$payment->reference}}</h3>
                                <p class="text-sm text-gray-600">{{$payment->amount}}</p>
                                <p class="text-xs text-gray-500">{{$payment->created_at}}</p>
                                <p class="text-xs text-blue-600">Ref: {{$payment->reference}}</p>
                            </div>
                            <div class="h-4 w-4 rounded-full bg-green-500"></div>
                        </div>
                    </div>
                @endforeach

                </div>
            </div>
        </div>
    </div>

</body>
</x-app-layout>

