<x-app-layout>
    <div class="container mx-auto py-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-4">New Employee</h1>
            <a href="{{ route('employees.index') }}" 
               class="text-lg text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow mr-2">
                Home
            </a>
            <a href="{{ route('employees.Create') }}"
               class="text-lg text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow mr-2">
                Create Employee
            </a>
            <a href="{{ route('employees.contracts') }}" 
               class="text-lg text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded shadow mr-2">
                Show Contracts
            </a>
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
                            <option value="{{ $employee->id }}">{{ $employee->user->first_name }} {{ $employee->user->last_name }}</option>
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
                            <option value="{{ $function->id }}">{{ $function->name }}</option>
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
</x-app-layout>
