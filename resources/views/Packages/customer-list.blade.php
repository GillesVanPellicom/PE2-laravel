<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Customer List</h1>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">First Name</th>
                        <th class="py-2 px-4 border-b">Last Name</th>
                        <th class="py-2 px-4 border-b">Email</th>
                        <th class="py-2 px-4 border-b">Phone</th>
                        <th class="py-2 px-4 border-b">Address</th>
                        <th class="py-2 px-4 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="py-2 px-4 border-b">{{ $customer->first_name }}</td>
                            <td class="py-2 px-4 border-b">{{ $customer->last_name }}</td>
                            <td class="py-2 px-4 border-b">{{ $customer->email ?? 'N/A' }}</td>
                            <td class="py-2 px-4 border-b">{{ $customer->phone ?? 'N/A' }}</td>
                            <td class="py-2 px-4 border-b">{{ $customer->address }}</td>
                            <td class="py-2 px-4 border-b">
                                <a href="{{ route('packages.bulk-order', [
                                    'first_name' => $customer->first_name,
                                    'last_name' => $customer->last_name,
                                    'email' => $customer->email,
                                    'phone' => $customer->phone,
                                    'address' => $customer->address,
                                    'delivery_method_id' => 3 // Assuming 1 is the ID for "Home Address"
                                ]) }}"
                                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Send Package
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>