<x-app-layout>
    @section("pageName", "Dispatcher")
    <div class="flex h-screen relative">
        <!-- Left: Distribution Centers -->
        <div class="w-1/6 bg-white p-4 overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Distribution Centers</h2>
            <div class="mb-4">
                <label for="city_filter" class="block text-sm font-medium text-gray-700">Filter by City:</label>
                <select id="city_filter" name="city_filter"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="-1">All Cities</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            <ul id="distribution_centers" class="space-y-2">
                @foreach($distributionCenters as $center)
                    <li class="p-2 bg-gray-100 rounded shadow hover:bg-gray-200 cursor-pointer"
                        data-city-id="{{ $center->city_id ?? '' }}" onclick="showPackages('{{ $center->id }}', '{{ $center->description }}')">
                        {{ $center->description }}
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Middle: Space for Modals -->
        <div class="flex-grow flex items-center justify-center bg-gray-50">
            <p class="text-gray-500 italic">Space reserved for modals or additional content</p>
        </div>

        <!-- Right: Employees -->
        <div class="absolute top-0 right-0 w-1/6 bg-white p-4 overflow-y-auto h-screen">
            <h2 class="text-xl font-bold mb-4">Employees</h2>
            <ul class="space-y-2">
                @foreach($employees as $employee)
                    <li class="p-2 bg-gray-100 rounded shadow flex justify-between items-center">
                        <span>{{ $employee->first_name }} {{ $employee->last_name }}</span>
                        <!-- Dots Menu -->
                        <div class="relative">
                            <button onclick="toggleMenu(this)" class="dots-menu-button">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 hover:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v.01M12 12v.01M12 18v.01" />
                                </svg>
                            </button>
                            <div class="dots-menu hidden absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded shadow-lg">
                                <ul class="py-1">
                                    <li><button class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left" onclick="viewEmployee('{{ $employee->id }}', '{{ $employee->first_name }} {{ $employee->last_name }}')">View</button></li>
                                    <li><button class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left" onclick="dispatchEmployee('{{ $employee->id }}', '{{ $employee->first_name }} {{ $employee->last_name }}')">Dispatch</button></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- View Employee Modal -->
    <div id="view_modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 id="view_modal_title" class="text-xl font-bold mb-4">Employee Details</h2>
            <p id="view_modal_content" class="text-gray-700 mb-4">Details about the employee will appear here.</p>
            <div class="flex justify-end">
                <button onclick="closeModal('view_modal')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Dispatch Modal -->
    <div id="dispatch_modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 id="dispatch_modal_title" class="text-xl font-bold mb-4">Dispatch Packages</h2>
            <p id="dispatch_modal_content" class="text-gray-700 mb-4">Select packages for the employee.</p>
            <div class="flex justify-end">
                <a href="#" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Dispatch Route
                </a>
                <button onclick="closeModal('dispatch_modal')" class="ml-2 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        const distributionCenters = document.querySelectorAll('#distribution_centers li');
        const cityFilter = document.getElementById('city_filter');
        const modal = document.getElementById('modal');
        const modalTitle = document.getElementById('modal_title');
        const modalContent = document.getElementById('modal_content');
        const closeModalButton = document.getElementById('close_modal');

        // Filter distribution centers by city
        cityFilter.addEventListener('change', () => {
            const selectedCity = cityFilter.value;
            distributionCenters.forEach(center => {
                if (selectedCity === '-1' || center.dataset.cityId === selectedCity) {
                    center.style.display = 'block';
                } else {
                    center.style.display = 'none';
                }
            });
        });

        // Show modal for a selected distribution center
        function showPackages(centerId, centerDescription) {
            modalTitle.textContent = `Distribution Center: ${centerDescription}`;
            modalContent.textContent = `Loading details for Distribution Center ID: ${centerId}...`;

            setTimeout(() => {
                modalContent.textContent = `Details for Distribution Center ID: ${centerId}`;
            }, 1000);

            modal.classList.remove('hidden');
        }

        // Close modal
        closeModalButton.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        // Toggle dots menu
        function toggleMenu(button) {
            // Close all other menus
            document.querySelectorAll('.dots-menu').forEach(menu => menu.classList.add('hidden'));
            // Toggle the current menu
            const menu = button.nextElementSibling;
            menu.classList.toggle('hidden');

            // Stop the click event from propagating to the document
            event.stopPropagation();
        }

        // Close all menus when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.dots-menu').forEach(menu => menu.classList.add('hidden'));
        });

        // View Employee Modal
        function viewEmployee(employeeId, employeeName) {
            const modal = document.getElementById('view_modal');
            document.getElementById('view_modal_title').textContent = `Employee: ${employeeName}`;
            document.getElementById('view_modal_content').textContent = `Details for Employee ID: ${employeeId}`;
            modal.classList.remove('hidden');
        }

        // Dispatch Employee Modal
        function dispatchEmployee(employeeId, employeeName) {
            const modal = document.getElementById('dispatch_modal');
            document.getElementById('dispatch_modal_title').textContent = `Dispatch for: ${employeeName}`;
            document.getElementById('dispatch_modal_content').textContent = `Select packages for Employee ID: ${employeeId}`;
            modal.classList.remove('hidden');
        }

        // Close Modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Example actions for the dots menu
        function viewEmployee(employeeId) {
            alert(`View details for Employee ID: ${employeeId}`);
        }

        function editEmployee(employeeId) {
            alert(`Edit Employee ID: ${employeeId}`);
        }

        function deleteEmployee(employeeId) {
            if (confirm(`Are you sure you want to delete Employee ID: ${employeeId}?`)) {
                alert(`Employee ID: ${employeeId} deleted.`);
            }
        }
    </script>
</x-app-layout>