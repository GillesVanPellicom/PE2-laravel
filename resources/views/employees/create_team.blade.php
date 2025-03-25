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
            <h1 class="text-4xl font-bold mb-4">Create Team</h1>
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

        <div class="max-w-3xl mx-auto p-6 bg-white rounded shadow-lg mt-8">
            <form action="{{ route('employees.store_team') }}" method="POST" class="space-y-6">
                @csrf

                <div class="mb-4">
                    <label for="department" class="block text-sm font-medium text-gray-700">Department:</label>
                    <input type="text" name="department" id="department" value="{{ old('department') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('department')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="employee" class="block text-sm font-medium text-gray-700">Manager:</label>
                    <select name="employee" id="employee"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Manager</option>
                        @foreach ($employees as $employee)
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

                <div>
                    <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded shadow">
                        Create Team
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>