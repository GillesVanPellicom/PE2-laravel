<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @section("pageName", "Dispatcher")
    <div class="flex h-screen">
        <!-- Left sidebar with distribution centers -->
        <div class="w-1/6 bg-white p-4 overflow-y-auto border-r">
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

        <!-- Main content area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="p-6 flex flex-col h-full">
                <h2 class="text-2xl font-bold sticky top-0 bg-white z-10 pb-4">
                    @if(isset($distributionCenter) && $distributionCenter)
                        {{ $distributionCenter->description }}
                    @else
                        Select a Distribution Center
                    @endif
                </h2>

                <div id="package-content" class="flex-1 overflow-y-auto">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Right sidebar with employees -->
        <div class="w-1/6 bg-white p-4 overflow-y-auto border-l">
            <h2 class="text-xl font-bold mb-4">Couriers</h2>
            <ul class="space-y-2">
                @foreach($employees as $employee)
                    <li class="employee-item p-2 bg-gray-100 rounded shadow flex justify-between items-center"
                        data-employee-id="{{ $employee->employee->id }}">
                        <span>{{ $employee->first_name }} {{ $employee->last_name }}</span>
                        <div class="relative">
                            <button onclick="toggleMenu(this)" class="dots-menu-button">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 hover:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v.01M12 12v.01M12 18v.01" />
                                </svg>
                            </button>
                            <div class="dots-menu hidden absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded shadow-lg z-50">
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

    <!-- Modals -->
    <div id="view_modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 id="view_modal_title" class="text-xl font-bold mb-4">Employee Details</h2>
            <p id="view_modal_content" class="text-gray-700 mb-4">Details about the employee will appear here.</p>
            <div class="flex justify-end">
                <button onclick="closeModal('view_modal')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Close
                </button>
            </div>
        </div>
    </div>

<!-- Vervang de bestaande dispatch modal met deze versie -->
<div id="dispatch_modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-1/3">
        <h2 id="dispatch_modal_title" class="text-xl font-bold mb-4">Select Employee for Dispatch</h2>
        <div id="dispatch_modal_content" class="text-gray-700 mb-4">
            <!-- Employee selection will be dynamically inserted here -->
        </div>
        <div class="flex justify-end">
            <button onclick="closeModal('dispatch_modal')" 
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">
                Cancel
            </button>
            <button onclick="confirmDispatch()"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Confirm Dispatch
            </button>
        </div>
    </div>
</div>

    <script>
        // Variables to store current DC info
        let currentDcId = null;
        let currentDcDescription = null;

        const distributionCenters = document.querySelectorAll('#distribution_centers li');
        const cityFilter = document.getElementById('city_filter');

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
                console.log('Starting showPackages with:', { centerId, centerDescription });
                currentDcId = centerId;
                currentDcDescription = centerDescription;
                const cityId = document.getElementById('city_filter').value;

                document.getElementById('package-content').innerHTML = `
                    <div class="p-4 bg-gray-100 text-gray-700 rounded">
                        Loading packages...
                    </div>`;

                console.log('Making fetch request to:', `/distribution-center/${centerId}?city_id=${cityId}`);

                const response = await fetch(`/distribution-center/${centerId}?city_id=${cityId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('Response status:', response.status);
                const responseText = await response.text();
                console.log('Response text:', responseText);

                // Try to parse the response as JSON
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    throw new Error('Invalid JSON response');
                }

                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }

                console.log('Parsed data:', data);
                updatePackageDisplay(data, centerDescription);
            } catch (error) {
                console.error('Error in showPackages:', error);
                document.getElementById('package-content').innerHTML = `
                    <div class="p-4 bg-red-100 text-red-700 rounded">
                        Error loading packages: ${error.message}
                    </div>`;
            }
        }


        function updatePackageDisplay(data, centerDescription) {
            console.log('Updating package display with:', { data, centerDescription });
            document.querySelector('.text-2xl.font-bold').textContent = centerDescription;
            
            let html = `
                <div class="space-y-6">
                    <!-- Unassigned Packages Section -->
                    <div class="mb-6">
                        <h2 class="text-xl font-bold mb-4">Unassigned Packages</h2>
                        <div class="grid grid-cols-2 gap-4">
                            ${data.unassignedGroups.map(group => `
                                <div class="bg-white p-4 rounded-lg shadow">
                                    <div class="flex justify-between items-center mb-4 bg-white z-10">
                                        <div class="flex items-center gap-4">
                                            <h3 class="text-xl font-semibold">Going to: ${group.nextMovement} (${group.packages.length})</h3>
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox" 
                                                    id="select-all-unassigned-${group.city.toLowerCase()}" 
                                                    class="h-5 w-5 text-blue-600 rounded select-all-group"
                                                    data-city="${group.city}">
                                                <label for="select-all-unassigned-${group.city.toLowerCase()}" class="text-sm">Select All</label>
                                            </div>
                                        </div>
                                        <button onclick="dispatchSelectedPackages('${group.city}')" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                            Assign to Courier
                                        </button>
                                    </div>
                                    <div class="overflow-y-auto h-[300px] border rounded-lg">
                                        <div class="space-y-2 p-2">
                                            ${group.packages.map(package => `
                                                <div class="border p-4 rounded-md hover:bg-gray-50">
                                                    <div class="flex items-center gap-4">
                                                        <input type="checkbox" 
                                                            name="package" 
                                                            value="${package.ref}"
                                                            data-city="${group.city}"
                                                            class="h-5 w-5 text-blue-600 rounded package-checkbox">
                                                        <div class="flex-1">
                                                            <p class="font-medium">Reference: ${package.ref}</p>
                                                            <p class="text-sm text-gray-600">Destination: ${package.destination}</p>
                                                            <p class="text-sm text-gray-600">Next Stop: ${package.next_node}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Assigned Packages Section -->
                    <div>
                        <h2 class="text-xl font-bold mb-4">Assigned Packages</h2>
                        <div class="grid grid-cols-2 gap-4">
                            ${data.assignedGroups.map(group => `
                                <div class="bg-gray-50 p-4 rounded-lg shadow">
                                    <div class="flex justify-between items-center mb-4 bg-gray-50 z-10">
                                        <div class="flex items-center gap-4">
                                            <h3 class="text-xl font-semibold">Assigned for transport to: ${group.nextMovement} (${group.packages.length})</h3>
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox" 
                                                    id="select-all-assigned-${group.city.toLowerCase()}" 
                                                    class="h-5 w-5 text-blue-600 rounded select-all-group"
                                                    data-city="${group.city}">
                                                <label for="select-all-assigned-${group.city.toLowerCase()}" class="text-sm">Select All</label>
                                            </div>
                                        </div>
                                        <button onclick="unassignSelectedPackages('${group.city}')"
                                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                            Unassign Selected
                                        </button>
                                    </div>
                                    <div class="overflow-y-auto h-[300px] border rounded-lg bg-white">
                                        <div class="space-y-2 p-2">
                                            ${group.packages.map(package => `
                                                <div class="border p-4 rounded-md hover:bg-gray-50">
                                                    <div class="flex items-center gap-4">
                                                        <input type="checkbox" 
                                                            name="assigned_package" 
                                                            value="${package.ref}"
                                                            data-city="${group.city}"
                                                            class="h-5 w-5 text-blue-600 rounded">
                                                        <div class="flex-1">
                                                            <p class="font-medium">Reference: ${package.ref}</p>
                                                            <p class="text-sm text-gray-600">Destination: ${package.destination}</p>
                                                            <p class="text-sm text-gray-600">Next Stop: ${package.next_node}</p>
                                                            <p class="text-sm text-blue-600">Assigned to: ${package.courier}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('package-content').innerHTML = html;

                // Add event listeners for select-all checkboxes
            document.querySelectorAll('.select-all-group').forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    const city = e.target.dataset.city;
                    const isUnassigned = e.target.id.startsWith('select-all-unassigned');
                    const checkboxes = document.querySelectorAll(
                        isUnassigned 
                            ? `input[name="package"][data-city="${city}"]`
                            : `input[name="assigned_package"][data-city="${city}"]`
                    );
                    checkboxes.forEach(cb => cb.checked = e.target.checked);
                });
            });
        }

        async function dispatchSelectedPackages() {
            const selectedPackages = Array.from(document.querySelectorAll('input[name="package"]:checked'))
                .map(checkbox => checkbox.value);

            if (selectedPackages.length === 0) {
                alert('Please select packages to dispatch');
                return;
            }

            window.selectedPackagesForDispatch = selectedPackages;

            const modal = document.getElementById('dispatch_modal');
            document.getElementById('dispatch_modal_title').textContent = 'Select Employee for Dispatch';
            
            const employeesList = Array.from(document.querySelectorAll('.employee-item')).map(emp => {
                const name = emp.querySelector('span').textContent;
                const id = emp.dataset.employeeId;
                return `
                    <div class="mb-2">
                        <label class="flex items-center space-x-3 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="selected_employee" value="${id}" class="h-4 w-4 text-blue-600">
                            <span>${name}</span>
                        </label>
                    </div>
                `;
            }).join('');

            document.getElementById('dispatch_modal_content').innerHTML = `
                <div class="max-h-60 overflow-y-auto">
                    ${employeesList}
                </div>
            `;

            modal.classList.remove('hidden');
        }

        // Add this new function
        async function unassignSelectedPackages(city) {
            const selectedPackages = Array.from(
                document.querySelectorAll(`input[name="assigned_package"][data-city="${city}"]:checked`)
            ).map(checkbox => checkbox.value);

            if (selectedPackages.length === 0) {
                alert('Please select packages to unassign');
                return;
            }

            if (!confirm('Are you sure you want to unassign these packages?')) {
                return;
            }

            try {
                const response = await fetch('/distribution-center/unassign-packages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ packages: selectedPackages })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to unassign packages');
                }

                showPackages(currentDcId, currentDcDescription);
                alert('Packages successfully unassigned');

            } catch (error) {
                console.error('Error:', error);
                alert(error.message);
            }
        }
        async function confirmDispatch() {
            const selectedEmployee = document.querySelector('input[name="selected_employee"]:checked');
            if (!selectedEmployee) {
                alert('Please select an employee');
                return;
            }

            try {
                const response = await fetch('/distribution-center/dispatch-packages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        packages: window.selectedPackagesForDispatch,
                        employee_id: selectedEmployee.value
                    })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to dispatch packages');
                }

                closeModal('dispatch_modal');
                showPackages(currentDcId, currentDcDescription);
                alert('Packages successfully assigned to courier');

            } catch (error) {
                console.error('Error:', error);
                alert(error.message);
            }
        }

        async function processSelectedPackages() {
            const selectedPackages = Array.from(document.querySelectorAll('input[name="stock_package"]:checked'))
                .map(checkbox => checkbox.value);

            if (selectedPackages.length === 0) {
                alert('Please select packages to process');
                return;
            }

            try {
                const response = await fetch('/distribution-center/process-packages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ packages: selectedPackages })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to process packages');
                }

                showPackages(currentDcId, currentDcDescription);
                alert(data.message);
            } catch (error) {
                console.error('Error:', error);
                alert(error.message);
            }
        }

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
    </script>
</x-app-layout>