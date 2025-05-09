<x-app-layout>
    @section("title","Stranded Packages")



    <div class="min-h-[calc(100vh-121px)] flex flex-col justify-center items-center bg-gray-100 px-2 py-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            Stranded Packages </h1>

        <div class="w-full max-w-5xl bg-white rounded-lg shadow-xl p-6">
            <form method="POST" action="{{ route('workspace.stranded-packages') }}">
                @csrf
                <div class="mb-4 flex justify-between items-center rounded-lg">
                    <span class="text-lg font-semibold text-gray-700">
                        @if(count($packages))
                            Select stranded packages below:
                        @else
                            No stranded packages found.
                        @endif
                    </span>
                    @if(count($packages))
                        <button
                            type="button"
                            id="select-all-btn"
                            class="bg-blue-200 text-blue-700 px-4 py-1 rounded hover:bg-blue-500 hover:text-white font-semibold transition"
                        >
                            Select All Stranded Parcels
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto rounded-lg">
                    <table class="min-w-full table-auto rounded-lg overflow-hidden shadow border border-gray-200 ">
                        <thead class="bg-blue-100">
                        <tr>
                            <th class="px-4 py-3 border-b text-center w-12">
                                <input type="checkbox" id="select-all" class="form-checkbox h-5 w-5 text-blue-600 transition">
                            </th>
                            <th class="px-4 py-3 border-b text-left font-semibold">Package ID</th>
                            <th class="px-4 py-3 border-b text-left font-semibold">Status</th>
                            <th class="px-4 py-3 border-b text-left font-semibold">Location</th>
                            <th class="px-4 py-3 border-b text-left font-semibold">Details</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @forelse($packages as $package)
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-2 text-center w-12">
                                    <input type="checkbox"
                                           name="selected_packages[]"
                                           value="{{ $package->reference }}"
                                           class="row-checkbox form-checkbox h-5 w-5 text-blue-600 transition">
                                </td>
                                <td class="px-4 py-2 text-gray-900 font-medium">{{ $package->reference }}</td>
                                <td class="px-4 py-2">
            <span class="inline-block px-2 py-1 rounded text-xs
                {{ $package->status === 'stranded' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-500' }}
            ">{{ ucfirst($package->status) }}</span>
                                </td>
                                <td class="px-4 py-2 text-gray-600">{{ $package->location }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $package->details }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-8 text-gray-400 text-lg">
                                    <i class="fa-solid fa-cube box-icon text-5xl mb-2"></i>
                                    <div>No stranded packages found.</div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-6">
                    <button
                        type="submit"
                        id="action-btn"
                        class="transition duration-150 px-8 py-2 bg-blue-600 text-white font-semibold rounded shadow hover:bg-blue-700 focus:outline-none disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed"
                        @if (empty($packages) || !$packages->count()) disabled @endif
                    >
                        Re-calculate route
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Only attach JS if there are packages
        @if(count($packages))
        const selectAll = document.getElementById('select-all');
        const selectAllBtn = document.getElementById('select-all-btn');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const actionBtn = document.getElementById('action-btn');

        function updateSelectAllState() {
            selectAll.checked = Array.from(checkboxes).every(chk => chk.checked);
        }

        function updateActionBtnState() {
            actionBtn.disabled = Array.from(checkboxes).filter(chk => chk.checked).length === 0;
        }

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(chk => chk.checked = this.checked);
            updateActionBtnState();
        });

        checkboxes.forEach(chk => {
            chk.addEventListener('change', function () {
                updateSelectAllState();
                updateActionBtnState();
            });
        });

        selectAllBtn.addEventListener('click', function () {
            const isSelecting = !(Array.from(checkboxes).every(chk => chk.checked));
            checkboxes.forEach(chk => chk.checked = isSelecting);
            selectAll.checked = isSelecting;
            updateActionBtnState();
        });

        updateActionBtnState();
        @endif
    </script>
</x-app-layout>
