<x-app-layout>

<x-sidebar>

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
                        class="w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow">
                        Create Team
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-sidebar>
</x-app-layout>