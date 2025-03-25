<x-app-layout>
    @section("pageName","Employees")

    <div class="flex">
        <div class="w-64 h-screen bg-gray-800 text-white">
            <div class="px-6 py-4">
                <h1 class="text-2xl font-semibold">Employee Dashboard</h1>
            </div>
            <ul class="mt-6 space-y-4">
                <li>
                    <a href="/employees" 
                    class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Employees List
                    </a>
                </li>
                <li>
                    <a href="/employees/create" 
                    class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Add Employee
                    </a>
                </li>
                <li>
                    <a href="/employees/contracts" 
                    class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Employee Contracts
                    </a>
                </li>
                <li>
                    <a href="/employees/create-contract" 
                    class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Add Contract
                    </a>
                </li>
                <li>
                    <a href="/employees/teams" 
                    class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Employee Teams
                    </a>
                </li>
                <li>
                    <a href="/employees/create-team" 
                    class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Add Team
                    </a>
                </li>
                <li>
                    <a href="/employees/functions" 
                    class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Employee Functions
                    </a>
                </li>
                <li>
                    <a href="/employees/create-function" 
                    class="block px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                    Add Function
                    </a>
                </li>
            </ul>
        </div>
    <div class="flex-1 p-6">

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
            </table>
        </div>
    </div>
    </div>
</x-app-layout>
