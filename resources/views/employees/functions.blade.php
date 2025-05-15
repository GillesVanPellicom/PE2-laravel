<x-app-layout>
    @section("pageName","Functions")

<x-sidebar>

    <div class="container mx-auto py-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-4">Functions</h1>
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
                        <th class="border border-gray-300 px-4 py-2">Role</th>
                        <th class="border border-gray-300 px-4 py-2">Description</th>
                        <th class="border border-gray-300 px-4 py-2">Salary Min</th>
                        <th class="border border-gray-300 px-4 py-2">Salary Max</th>
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
                                {{ $function->role }}
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 flex justify-center">
            {{ $functions->links() }}
        </div>
    </div>
</x-sidebar>


</x-app-layout>