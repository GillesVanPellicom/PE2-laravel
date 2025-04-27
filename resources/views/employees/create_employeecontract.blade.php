<x-app-layout>

<x-sidebar>

<div class="container mx-auto py-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-4">Create Employee Contract</h1>
        </div>
        <div class="max-w-3xl mx-auto p-6 bg-white rounded shadow-lg mt-8">

            <form action="{{ route('workspace.employees.store_contract') }}" method="POST" class="space-y-6">
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
                    <label for="location" class="block text-sm font-medium text-gray-700">Location:</label>
                    <select name="location" id="location"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="-1">Select a location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}"
                            @if(old('location') == $location->id) selected @endif>
                            {{ $location->description }}</option>
                        @endforeach
                    </select>
                    @error('location')
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
                    <label for="vacation_days" class="block text-sm font-medium text-gray-700">Enter the amount of vacation days this employee gets:</label>
                    <input type="number" name="vacation_days" id="vacation_days" value="{{ old('vacation_days') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('vacation_days')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                        class="w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow">
                        Create Contract
                    </button>
                </div>
            </form>
        </div>
</x-sidebar>
</x-app-layout>
