<x-app-layout>
    @section("pageName", "Dispatcher")
    <div class="flex h-screen relative">
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

        <div class="flex-grow flex items-center justify-center bg-gray-50">
            <p class="text-gray-500 italic">Space reserved for modals or additional content</p>
        </div>

        <div class="absolute top-0 right-0 w-1/6 bg-white p-4 overflow-y-auto h-screen">
            <h2 class="text-xl font-bold mb-4">Employees</h2>
            <ul class="space-y-2">
                @foreach($employees as $employee)
                    <li class="p-2 bg-gray-100 rounded shadow flex justify-between items-center">
                        <span>{{ $employee->first_name }} {{ $employee->last_name }}</span>
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
        const middleSection = document.querySelector('.flex-grow');

        cityFilter.addEventListener('change', () => {
            const selectedCityId = cityFilter.value;

            distributionCenters.forEach(center => {
                if (selectedCityId === '-1' || center.dataset.cityId === selectedCityId) {
                    center.style.display = 'block'; 
                } else {
                    center.style.display = 'none'; 
                }
            });
        });

        async function showPackages(centerId, centerDescription) {
            try {
                const response = await fetch(`/api/distribution-center/${centerId}`);
                const data = await response.json();

                if (response.status !== 200) {
                    alert(data.error || 'Failed to fetch data');
                    return;
                }

                const readyToDeliverHtml = data.readyToDeliver.map(pkg => `
                    <li class="p-2 bg-green-100 rounded shadow">
                        <strong>Reference:</strong> ${pkg.ref}<br>
                        <strong>Destination:</strong> ${pkg.destination}
                    </li>
                `).join('');

                const inStockHtml = data.inStock.map(pkg => `
                    <li class="p-2 bg-yellow-100 rounded shadow">
                        <strong>Reference:</strong> ${pkg.ref}<br>
                        <strong>Next Destination:</strong> ${pkg.nextDestination}
                    </li>
                `).join('');

                const hardcodedHtml = `
                    <div class="bg-white p-6 rounded shadow-lg w-full h-full flex flex-col">
                        <h2 class="text-2xl font-bold mb-4">${centerDescription}</h2>
                        <p class="text-gray-700 mb-4">Overview of packages in this distribution center.</p>
                        <h3 class="text-xl font-bold mb-2">Packages Ready to Deliver</h3>
                        <ul class="space-y-2 flex-grow overflow-y-auto">
                            ${readyToDeliverHtml || '<p class="text-gray-500">No packages ready to deliver.</p>'}
                        </ul>
                        <h3 class="text-xl font-bold mb-2 mt-4">Packages in Stock</h3>
                        <ul class="space-y-2 flex-grow overflow-y-auto">
                            ${inStockHtml || '<p class="text-gray-500">No packages in stock.</p>'}
                        </ul>
                    </div>
                `;

                middleSection.innerHTML = hardcodedHtml;
                middleSection.classList.add('h-full');
            } catch (error) {
                console.error('Error fetching packages:', error);
                alert('An error occurred while fetching packages.');
            }
        }

        // const closeModalButton = document.getElementById('close_modal');
        // closeModalButton.addEventListener('click', () => {
        //     modal.classList.add('hidden');
        // });

        function toggleMenu(button) {
            document.querySelectorAll('.dots-menu').forEach(menu => menu.classList.add('hidden'));
            const menu = button.nextElementSibling;
            menu.classList.toggle('hidden');

            event.stopPropagation();
        }

        document.addEventListener('click', () => {
            document.querySelectorAll('.dots-menu').forEach(menu => menu.classList.add('hidden'));
        });

        function viewEmployee(employeeId, employeeName) {
            const modal = document.getElementById('view_modal');
            document.getElementById('view_modal_title').textContent = `Employee: ${employeeName}`;
            document.getElementById('view_modal_content').textContent = `Details for Employee ID: ${employeeId}`;
            modal.classList.remove('hidden');
        }

        function dispatchEmployee(employeeId, employeeName) {
            const modal = document.getElementById('dispatch_modal');
            document.getElementById('dispatch_modal_title').textContent = `Dispatch for: ${employeeName}`;
            document.getElementById('dispatch_modal_content').textContent = `Select packages for Employee ID: ${employeeId}`;
            modal.classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

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