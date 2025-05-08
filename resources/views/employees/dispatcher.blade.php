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

                <div id="package-list">
                    <!-- Pakketten worden hier dynamisch geladen -->
                </div>

                <div id="pagination" class="flex justify-between items-center mt-4">
                    <!-- Paginering wordt hier dynamisch geladen -->
                </div>
            </div>
        </div>

        <!-- Right sidebar with employees -->
        <div class="w-1/6 bg-white p-4 overflow-y-auto border-l">
            <h2 class="text-xl font-bold mb-4">Couriers</h2>
            <ul class="space-y-2">
                @foreach($employees as $employee)
                    <li class="employee-item p-2 rounded shadow flex justify-between items-center
                        {{ $employee->employee && $employee->employee->packageMovements()->whereNull('departure_time')->exists() ? 'bg-green-100' : 'bg-gray-100' }}"
                        data-employee-id="{{ $employee->employee_id }}">
                        <span>{{ $employee->first_name }} {{ $employee->last_name }}</span>
                        <button onclick="viewEmployee('{{ $employee->employee_id }}', '{{ $employee->first_name }} {{ $employee->last_name }}')"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-2 rounded">
                            View
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Modals -->
    <div id="view_modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 id="view_modal_title" class="text-xl font-bold mb-4">Courier Details</h2>
            <div id="view_modal_content" class="text-gray-700 mb-4">
                <!-- Courier details will be dynamically loaded here -->
            </div>
            <div class="flex justify-end">
                <button onclick="closeModal('view_modal')" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Close
                </button>
            </div>
        </div>
    </div>

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
                currentDcId = centerId;
                currentDcDescription = centerDescription;
                const cityId = document.getElementById('city_filter').value;

                document.getElementById('package-content').innerHTML = `
                    <div class="p-4 text-center">
                        <p>Loading packages...</p>
                    </div>
                `;

                const response = await fetch(`/workspace/distribution-center/${centerId}?city_id=${cityId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to load packages');
                }

                updatePackageDisplay(data, centerDescription);
            } catch (error) {
                console.error('Error in showPackages:', error);
                document.getElementById('package-content').innerHTML = `
                    <div class="p-4 bg-red-100 text-red-700 rounded">
                        Error loading packages: ${error.message}
                    </div>
                `;
            }
        }

        function updatePackageDisplay(data, centerDescription) {
            document.querySelector('.text-2xl.font-bold').textContent = centerDescription;

            let html = `
                <div class="space-y-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold mb-4">Unassigned Packages</h2>
                        <div class="grid grid-cols-2 gap-4">
                            ${data.unassignedGroups.map(group => `
                                <div class="bg-white p-4 rounded-lg shadow">
                                    <div class="flex justify-between items-center mb-4">
                                        <div class="flex items-center gap-4">
                                            <h3 class="text-xl font-semibold">
                                                Going to: ${group.nextMovement}
                                                <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                                    ${group.packages.length} packages
                                                </span>
                                            </h3>
                                            <input type="checkbox" 
                                                id="select-all-unassigned-${group.city}" 
                                                class="select-all-group"
                                                data-city="${group.city}">
                                            <label for="select-all-unassigned-${group.city}">Select All</label>
                                        </div>
                                        <button onclick="dispatchSelectedPackages('${group.city}')" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                            Assign to Courier
                                        </button>
                                    </div>
                                    <div class="overflow-y-auto max-h-[300px] border rounded-lg">
                                        <div class="space-y-2 p-4">
                                            ${group.packages.map(package => `
                                                <div class="border p-4 rounded-md hover:bg-gray-50">
                                                    <div class="flex items-center gap-4">
                                                        <input type="checkbox" 
                                                            name="package" 
                                                            value="${package.ref}"
                                                            data-city="${group.city}"
                                                            class="h-5 w-5">
                                                        <div class="flex-1">
                                                            <p class="font-medium">Reference: ${package.ref}</p>
                                                            <p class="text-sm text-gray-600">Next Stop: ${package.next_node}</p>
                                                            <p class="text-sm text-gray-600">Final Destination: ${package.destination}</p>
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

                    <div>
                        <h2 class="text-xl font-bold mb-4">Assigned Packages</h2>
                        <div class="grid grid-cols-2 gap-4">
                            ${data.assignedGroups.map(group => `
                                <div class="bg-gray-50 p-4 rounded-lg shadow">
                                    <div class="flex justify-between items-center mb-4">
                                        <div class="flex items-center gap-4">
                                            <h3 class="text-xl font-semibold">
                                                Going to: ${group.nextMovement}
                                                <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                                                    ${group.packages.length} packages
                                                </span>
                                            </h3>
                                            <input type="checkbox" 
                                                id="select-all-assigned-${group.city}" 
                                                class="select-all-group"
                                                data-city="${group.city}">
                                            <label for="select-all-assigned-${group.city}">Select All</label>
                                        </div>
                                        <button onclick="unassignSelectedPackages('${group.city}')"
                                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                            Unassign Selected
                                        </button>
                                    </div>
                                    <div class="overflow-y-auto max-h-[300px] border rounded-lg">
                                        <div class="space-y-2 p-4">
                                            ${group.packages.map(package => `
                                                <div class="border p-4 rounded-md hover:bg-gray-50">
                                                    <div class="flex items-center gap-4">
                                                        <input type="checkbox" 
                                                            name="assigned_package" 
                                                            value="${package.ref}"
                                                            data-city="${group.city}"
                                                            class="h-5 w-5">
                                                        <div class="flex-1">
                                                            <p class="font-medium">Reference: ${package.ref}</p>
                                                            <p class="text-sm text-gray-600">Next Stop: ${package.next_node}</p>
                                                            <p class="text-sm text-gray-600">Final Destination: ${package.destination}</p>
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
            
            const employeesList = Array.from(document.querySelectorAll('.employee-item'))
                .filter(emp => !emp.classList.contains('assigned'))
                .map(emp => {
                    const name = emp.querySelector('span').textContent;
                    const id = emp.dataset.employeeId;
                    return `
                        <div class="mb-2 p-2 hover:bg-gray-100 rounded">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="selected_employee" value="${id}" class="form-radio">
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

        async function unassignSelectedPackages(city) {
            try {
                const selectedPackages = Array.from(
                    document.querySelectorAll(`input[name="assigned_package"][data-city="${city}"]:checked`)
                ).map(checkbox => checkbox.value);

                if (selectedPackages.length === 0) {
                    alert('Please select packages to unassign');
                    return;
                }

                const response = await fetch('/workspace/distribution-center/unassign-packages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ packages: selectedPackages })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to unassign packages');
                }

                await showPackages(currentDcId, currentDcDescription);
        
                document.querySelectorAll('.employee-item.assigned').forEach(item => {
                    item.classList.remove('assigned');
                    item.style.display = '';
                });

                alert('Packages successfully unassigned');

            } catch (error) {
                console.error('Error:', error);
                alert(error.message);
            }
        }

        async function confirmDispatch() {
            try {
                const selectedEmployee = document.querySelector('input[name="selected_employee"]:checked');
                if (!selectedEmployee) {
                    alert('Please select an employee');
                    return;
                }

                const response = await fetch('/workspace/distribution-center/dispatch-packages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        packages: window.selectedPackagesForDispatch,
                        employee_id: selectedEmployee.value
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to dispatch packages');
                }

                const data = await response.json();

                if (!data || typeof data !== 'object') {
                    throw new Error('Invalid JSON response');
                }

                alert(data.message || 'Packages successfully assigned');
                closeModal('dispatch_modal');
                await showPackages(currentDcId, currentDcDescription);

            } catch (error) {
                console.error('Dispatch error:', error);
                alert(error.message);
            }
        }

        async function refreshCourierList() {
            try {
                const response = await fetch('/workspace/distribution-center/couriers', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to refresh courier list');
                }

                const couriers = await response.json();
            } catch (error) {
                console.error('Error refreshing courier list:', error);
            }
        }

        async function viewEmployee(employeeId, employeeName) {
            const modal = document.getElementById('view_modal');
            document.getElementById('view_modal_title').textContent = `Courier: ${employeeName}`;
            document.getElementById('view_modal_content').innerHTML = '<p>Loading...</p>';

            try {
                const response = await fetch(`/workspace/distribution-center/courier-route/${employeeId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    console.error('API Error:', data);
                    throw new Error(data.message || 'Failed to fetch courier details');
                }

                const routeDistance = data.route_distance || 0;
                const packages = Array.isArray(data.packages) ? data.packages : [];
                const uniquePackages = Array.from(new Map(packages.map(pkg => [pkg.reference, pkg])).values());
                let packageList = uniquePackages.map(pkg => `
                    <li class="p-2 border-b">
                        <strong>Reference:</strong> ${pkg.reference} <br>
                        <strong>Destination:</strong> ${pkg.destination_latitude && pkg.destination_longitude 
                            ? `(${pkg.destination_latitude}, ${pkg.destination_longitude})` 
                            : 'Unknown'}
                    </li>
                `).join('');
                if (!uniquePackages.length) packageList = '<p>No packages assigned</p>';

                document.getElementById('view_modal_content').innerHTML = `
                    <p><strong>Route Distance:</strong> ${routeDistance.toFixed(2)} km</p>
                    <p><strong>Assigned Packages:</strong></p>
                    <ul class="overflow-y-auto max-h-60">${packageList}</ul>
                `;
            } catch (error) {
                console.error('Error fetching courier details:', error);
                document.getElementById('view_modal_content').innerHTML = '<p>Error loading details</p>';
            }

            modal.classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function toggleMenu(button) {
            // Sluit alle andere menu's
            document.querySelectorAll('.dots-menu').forEach(menu => menu.classList.add('hidden'));

            // Toon/verberg het menu dat bij de knop hoort
            const menu = button.nextElementSibling;
            menu.classList.toggle('hidden');
            event.stopPropagation();
        }

        document.addEventListener('click', () => {
            // Sluit alle menu's wanneer ergens anders wordt geklikt
            document.querySelectorAll('.dots-menu').forEach(menu => menu.classList.add('hidden'));
        });

        function dispatchEmployee(employeeId, employeeName) {
            const modal = document.getElementById('dispatch_modal');
            document.getElementById('dispatch_modal_title').textContent = `Dispatch for: ${employeeName}`;
            document.getElementById('dispatch_modal_content').textContent = `Select packages for Employee ID: ${employeeId}`;
            modal.classList.remove('hidden');
        }

        async function fetchCourierPackages(courierId, page = 1) {
            try {
                const response = await fetch(`/workspace/distribution-center/courier-route/${courierId}?page=${page}`);
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to fetch courier packages');
                }

                // Update de pakkettenlijst
                updatePackageList(data.packages.data);

                // Update de paginering
                updatePagination(data.packages);
            } catch (error) {
                console.error('Error fetching courier packages:', error);
            }
        }

        function updatePackageList(packages) {
            const packageList = document.getElementById('package-list');
            packageList.innerHTML = packages.map(pkg => `
                <li>${pkg.reference} - ${pkg.destination_location_id}</li>
            `).join('');
        }

        function updatePagination(pagination) {
            const paginationContainer = document.getElementById('pagination');
            paginationContainer.innerHTML = `
                <button ${pagination.current_page === 1 ? 'disabled' : ''} onclick="fetchCourierPackages(${pagination.current_page - 1})">Previous</button>
                <span>Page ${pagination.current_page} of ${pagination.last_page}</span>
                <button ${pagination.current_page === pagination.last_page ? 'disabled' : ''} onclick="fetchCourierPackages(${pagination.current_page + 1})">Next</button>
            `;
        }
    </script>
</x-app-layout>