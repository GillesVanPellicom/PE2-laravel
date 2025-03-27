<x-app-layout>
    @section("pageName","Teams")

<x-sidebar>

    <div class="container mx-auto py-10">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-4">Teams</h1>
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
                        <th class="border border-gray-300 px-4 py-2">Department</th>
                        <th class="border border-gray-300 px-4 py-2">Team Leader</th>
                        <th class="border border-gray-300 px-4 py-2">Created At</th>
                        <th class="border border-gray-300 px-4 py-2">Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teams as $team)
                        <tr class="even:bg-gray-50 odd:bg-white">
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $team->id }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $team->department }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $team->manager->user->first_name }} {{ $team->manager->user->last_name }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $team->created_at }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $team->updated_at }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 flex justify-center">
            {{ $teams->links() }}
        </div>
    </div>
</x-sidebar>
</x-app-layout>