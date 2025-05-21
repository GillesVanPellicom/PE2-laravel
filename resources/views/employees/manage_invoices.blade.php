<x-app-layout>
    @section('title', 'Invoices')
    @section('scripts')
    @endsection

    <div class="flex flex-col items-center justify-center min-h-[calc(100vh-121px)] bg-gray-100 w-full p-4">
        <div class="w-full max-w-6xl bg-white p-6 rounded-lg shadow-md">
            {{-- Header with Manage Button --}}
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold">Invoices</h1>
                <a href="{{ route('manage-invoice-system') }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
                    Manage
                </a>
            </div>

            {{-- Search and Filter --}}
            <div class="mb-6 flex flex-col justify-between md:flex-row md:items-center gap-4">
                <form method="GET" class="flex  items-center gap-2 w-full md:w-auto">
                    <label for="status" class="font-semibold whitespace-nowrap">Filter by Status:</label>
                    <select name="status" id="status" onchange="this.form.submit()" class="border rounded px-3 py-1">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </form>
                <div class="w-full md:w-80 relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <!-- magnifying glass SVG -->
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                          <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" fill="none"/>
                          <line x1="21" y1="21" x2="16.65" y2="16.65" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        id="invoice-table-search"
                        placeholder="Search invoices..."
                        class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-100 focus:outline-none transition"
                        autocomplete="off"
                    >
                </div>
            </div>

            {{-- Table with scrollable container --}}
            <div class="overflow-x-auto w-full">
                <div class="w-full" style="max-height: 480px; overflow-y: auto; position: relative;">
                    <table class="min-w-full table-auto border border-gray-200 text-sm" id="invoice-table">
                        <thead>
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold sticky top-0 z-20 bg-gray-100" style="top: -1%;">ID</th>
                            <th class="px-4 py-2 text-left font-semibold sticky top-0 z-20 bg-gray-100" style="top: -1%;">Status</th>
                            <th class="px-4 py-2 text-left font-semibold sticky top-0 z-20 bg-gray-100" style="top: -1%;">Total</th>
                            <th class="px-4 py-2 text-left font-semibold sticky top-0 z-20 bg-gray-100" style="top: -1%;">Paid At</th>
                            <th class="px-4 py-2 text-left font-semibold sticky top-0 z-20 bg-gray-100" style="top: -1%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($invoices as $invoice)
                            <tr class="border-t invoice-row hover:bg-gray-50">
                                <td class="px-4 py-2 text-left" data-search="{{ strtolower($invoice->reference ?? '') }} {{ strtolower($invoice->company->company_name ?? '') }}">
                                    #{{ $invoice->reference }}
                                </td>
                                <td class="px-4 py-2 text-left">
                                    @if ($invoice->is_paid)
                                        <span class="text-green-600 font-semibold">Paid</span>
                                    @elseif (!$invoice->is_paid && $invoice->due_date && $invoice->due_date < now())
                                        <span class="text-red-600 font-semibold">Overdue</span>
                                    @else
                                        <span class="text-yellow-600 font-semibold">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-left">€{{ $invoice->payment_amount }}</td>
                                <td class="px-4 py-2 text-left">{{ $invoice->paid_at ?? "_" }}</td>
                                <td class="px-4 py-2 text-left">
                                    <a href="{{ route('generate-invoice', $invoice->id) }}" target="_blank" class="bg-blue-500 text-white px-2 py-1 rounded text-sm hover:bg-blue-600">
                                        Open PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center px-4 py-4 text-gray-500">
                                    No invoices found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtering Script, only search --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('invoice-table-search');
            const rows = document.querySelectorAll('#invoice-table tbody .invoice-row');

            searchInput.addEventListener('input', function () {
                const search = this.value.trim().toLowerCase();
                rows.forEach(row => {
                    const searchText = (
                        row.querySelector('td[data-search]')?.getAttribute('data-search') +
                        ' ' + row.innerText
                    ).toLowerCase();
                    if (!search || searchText.includes(search)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
</x-app-layout>
