<x-app-layout>
    @section("pageName","Contracts")

    <div class="container mx-auto py-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-4">Contracts</h1>
            <a href="{{ route('employees.index') }}" 
               class="text-lg text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow mr-2">
                Show Employees
            </a>
            <a href="{{ route('employees.Create') }}" 
               class="text-lg text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow">
                Create Employee
            </a>
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
                        <th class="border border-gray-300 px-4 py-2">Employee</th>
                        <th class="border border-gray-300 px-4 py-2">Start Date</th>
                        <th class="border border-gray-300 px-4 py-2">End Date</th>
                        <th class="border border-gray-300 px-4 py-2">Contract Status</th>
                        <th class="border border-gray-300 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contracts as $contract)
                        <tr class="even:bg-gray-50 odd:bg-white">
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->contract_id }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->employee->first_name }} {{ $contract->employee->last_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->start_date }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->end_date }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->contract_status }}</td>
                            <td class="border border-gray-300 px-4 py-2 flex justify-center">
                                <form action="{{ route('employee.contracts.updateEndDate', $contract->contract_id) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow">
                                        Set End Date to Today
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


</x-app-layout>