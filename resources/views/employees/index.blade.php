<x-app-layout>
    @section("pageName","Employees")

<x-sidebar>

    <div class="container mx-auto py-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-4">Employees</h1>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 mb-6 rounded shadow">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-center">
            <table class="table-auto border-collapse border border-gray-300 w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">ID</th>
                        <th class="border border-gray-300 px-4 py-2">Last Name</th>
                        <th class="border border-gray-300 px-4 py-2">First Name</th>
                        <th class="border border-gray-300 px-4 py-2">Email</th>
                        <th class="border border-gray-300 px-4 py-2">Phone Number</th>
                        <th class="border border-gray-300 px-4 py-2">Birth Date</th>
                        <th class="border border-gray-300 px-4 py-2">Address</th>
                        <th class="border border-gray-300 px-4 py-2">City</th>
                        <th class="border border-gray-300 px-4 py-2">Country</th>
                        <th class="border border-gray-300 px-4 py-2">Leave Balance</th>
                        <th class="border border-gray-300 px-4 py-2">Team</th>
                        <th class="border border-gray-300 px-4 py-2">Created At</th>
                        <th class="border border-gray-300 px-4 py-2">Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                        <tr class="even:bg-gray-50 odd:bg-white">
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->employee->id }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->last_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->first_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->email }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->phone_number }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->birth_date }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $employee->address->street }} {{ $employee->address->house_number }} {{ $employee->address->bus_number }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->address->city->name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->address->city->country->country_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->employee->leave_balance }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->employee->team->department }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->created_at }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $employee->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>

                <button>
                    <a href="{{ route('employees.upgrade') }}"
                        class="w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow">
                        upgrade jane
                    </a>
                </button>

            </table>
        </div>
    </div>
</x-sidebar>
</x-app-layout>
