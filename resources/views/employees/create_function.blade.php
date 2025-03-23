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
            <h1 class="text-4xl font-bold mb-4">Create Function</h1>
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
            <form action="{{ route('employees.store_function') }}" method="POST" class="space-y-6">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Function:</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                    <textarea name="description" id="description"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="salary_min" class="block text-sm font-medium text-gray-700">Salary Min:</label>
                    <input type="number" name="salary_min" id="salary_min" value="{{ old('salary_min') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('salary_min')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="salary_max" class="block text-sm font-medium text-gray-700">Salary Max:</label>
                    <input type="number" name="salary_max" id="salary_max" value="{{ old('salary_max') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('salary_max')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded shadow">
                        Create Function
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
