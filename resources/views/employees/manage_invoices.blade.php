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

            {{-- Filter Dropdown --}}
            <form method="GET" class="mb-4">
                <label for="status" class="mr-2 font-semibold">Filter by Status:</label>
                <select name="status" id="status" onchange="this.form.submit()" class="border rounded px-3 py-1">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </form>

            {{-- Invoices Table --}}
            <table class="min-w-full table-auto border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Total</th>
                        <th class="px-4 py-2 text-left">Paid At</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($invoices as $invoice)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $invoice->id }}</td>
                        <td class="px-4 py-2">
                            @if ($invoice->is_paid)
                                <span class="text-green-600 font-semibold">Paid</span>
                            @elseif ($invoice->due_date < now())
                                <span class="text-red-600 font-semibold">Overdue</span>
                            @else
                                <span class="text-yellow-600 font-semibold">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">€{{ $invoice->total }}</td>
                        <td class="px-4 py-2">{{ $invoice->paid_at ?? '-' }}</td>
                        <td class="px-4 py-2 flex flex-wrap gap-2">
                            @if (! $invoice->is_paid)
                                <form method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="bg-green-500 text-white px-2 py-1 rounded text-sm hover:bg-green-600">
                                        Mark as Paid
                                    </button>
                                </form>
                            @endif
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
</x-app-layout>
