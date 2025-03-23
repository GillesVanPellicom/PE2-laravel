<x-app-layout>

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
            <h1 class="text-4xl font-bold mb-4">Create Employee Contract</h1>
        </div>
        <div class="max-w-3xl mx-auto p-6 bg-white rounded shadow-lg mt-8">
        
            <form action="{{ route('employees.store_contract') }}" method="POST" class="space-y-6">
                @csrf
                @method('POST')

                <div class="mb-4">
                    <label for="employee" class="block text-sm font-medium text-gray-700">Employee:</label>
                    <select name="employee" id="employee"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="-1">Select an employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" 
                                @if(old('employee') == $employee->id) selected @endif>
                                {{ $employee->user->first_name }} {{ $employee->user->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="function" class="block text-sm font-medium text-gray-700">Function:</label>
                    <select name="function" id="function"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="-1">Select a function</option>
                        @foreach($functions as $function)
                            <option value="{{ $function->id }}"
                            @if(old('function') == $function->id) selected @endif>    
                            {{ $function->name }}</option>
                        @endforeach
                    </select>
                    @error('function')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="vacation_days" class="block text-sm font-medium text-gray-700">Enter the amount of vacation days this employee gets:</label>
                    <input type="number" name="vacation_days" id="vacation_days" value="{{ old('vacation_days') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('vacation_days')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded shadow">
                        Create Contract
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
