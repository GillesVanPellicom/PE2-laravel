<x-app-layout>
    @section("pageName","Contracts")

    <div class="container mx-auto py-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-4">Contracts</h1>
            <a href="{{ route('employees.index') }}" 
               class="text-lg text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow mr-2">
                Home
            </a>
            <a href="{{ route('employees.Create') }}"
               class="text-lg text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow mr-2">
                Create Employee
            </a>
            <a href="{{ route('employees.create_contract') }}" 
               class="text-lg text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow mr-2">
                Create Contract
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 mb-6 rounded shadow">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="bg-red-100 text-red-800 p-4 mb-6 rounded shadow">
                {{ session('error') }}
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
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->employee->user->first_name }} {{ $contract->employee->user->last_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->start_date }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $contract->end_date }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if($contract->end_date > now() or $contract->end_date == NULL)
                                    Active
                                @else
                                    Expired
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2 flex justify-center">
                                @if($contract->end_date < now() or $contract->end_date == NULL)
                                <form action="{{ route('employee.contracts.updateEndDate', $contract->contract_id) }}" method="POST" class="flex items-center space-x-2">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 px-2 py-1 rounded shadow text-xs">
                                        Set End Date to:
                                    </button>
                                    <input name="end_date" type="date" value="{{ date('Y-m-d') }}" class="text-xs">
                                </form>
                                @else
                                <button type="submit" disabled class="text-white bg-gray-400 hover:bg-gray-500 px-4 py-2 rounded-md shadow-md text-xs cursor-not-allowed opacity-50">
                                    End date already set
                                </button>
                                @endif

                                <!--
                                <form action="{{ route('employee.contracts.updateEndDate', $contract->contract_id) }}" method="POST" class="flex items-center space-x-2">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 px-2 py-1 rounded shadow text-xs">
                                        Set End Date to:
                                    </button>
                                    <input name="end_date" type="date" value="{{ date('Y-m-d') }}" class="text-xs">
                                </form>
                                -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>