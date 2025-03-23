<x-app-layout>
    @section("pageName","Functions")

    <div class="container mx-auto py-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-4">Functions</h1>
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
                        <th class="border border-gray-300 px-4 py-2">Function</th>
                        <th class="border border-gray-300 px-4 py-2">Description</th>
                        <th class="border border-gray-300 px-4 py-2">Salary Min</th>
                        <th class="border border-gray-300 px-4 py-2">Salary Max</th>
                        <th class="border border-gray-300 px-4 py-2">Created At</th>
                        <th class="border border-gray-300 px-4 py-2">Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($functions as $function)
                        <tr class="even:bg-gray-50 odd:bg-white">
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $function->id }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $function->name }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $function->description }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $function->salary_min }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $function->salary_max }}
                            </td>
                            <td
                                class="border border-gray-300 px-4 py-2">{{ $function->created_at }}</td>
                            <td
                                class="border border-gray-300 px-4 py-2">{{ $function->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


</x-app-layout>