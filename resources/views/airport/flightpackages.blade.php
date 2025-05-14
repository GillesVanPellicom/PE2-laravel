<x-app-layout>
<x-sidebar-airport>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Flight Packages</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <h1 class="text-2xl font-bold mb-4">
        Packages at {{ $employeeLocationName ?? 'Your Location' }}
    </h1>

    <!-- Unassigned Packages Table -->
    <h2 class="text-xl font-semibold mb-2">Unassigned Packages</h2>
    <div class="overflow-x-auto mb-8">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">Package Reference</th>
                    <th class="py-2 px-4 border">Weight</th>
                    <th class="py-2 px-4 border">Next Movement</th>
                    <th class="py-2 px-4 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sortedPackages = $packages->sortBy(fn($package) => $package->getNextMovement()?->getID() ?? 'N/A');
                @endphp
                @forelse($sortedPackages->filter(fn($package) => !$package->assigned_flight && !str_starts_with($package->getNextMovement()?->getID(), '@DC_')) as $package)
                <tr class="border-b">
                    <td class="py-2 px-4 border">{{ $package->reference }}</td>
                    <td class="py-2 px-4 border">{{ $package->weight }} kg</td>
                    <td class="py-2 px-4 border">
                        @php
                            $nextMovement = $package->getNextMovement();
                        @endphp
                        {{ $nextMovement ? $nextMovement->getID() : 'N/A' }}
                    </td>
                    <td class="py-2 px-4 border">
                        <input type="checkbox" class="package-checkbox" value="{{ $package->id }}">
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-2 px-4 border text-center text-gray-600">No unassigned packages available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="text-right mt-4">
            <button onclick="openFlightModal()" 
                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                Assign to Flight
            </button>
        </div>
    </div>

    <!-- Packages to Distribution Center Table -->
    <h2 class="text-xl font-semibold mb-2">Packages to Distribution Center</h2>
    <div class="overflow-x-auto mb-8">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">Package Reference</th>
                    <th class="py-2 px-4 border">Weight</th>
                    <th class="py-2 px-4 border">Next Movement</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages->filter(fn($package) => $package->getNextMovement()?->getID() && str_starts_with($package->getNextMovement()->getID(), '@DC_')) as $package)
                <tr class="border-b">
                    <td class="py-2 px-4 border">{{ $package->reference }}</td>
                    <td class="py-2 px-4 border">{{ $package->weight }} kg</td>
                    <td class="py-2 px-4 border">{{ $package->getNextMovement()?->getID() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-2 px-4 border text-center text-gray-600">No packages need to go to the distribution center.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Reassign Packages Table -->
    <h2 class="text-xl font-semibold mb-2">Packages to be Reassigned</h2>
    <div class="overflow-x-auto mb-8">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">Package Reference</th>
                    <th class="py-2 px-4 border">Weight</th>
                    <th class="py-2 px-4 border">Currently Assigned Flight</th>
                    <th class="py-2 px-4 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages->filter(fn($package) => $package->assigned_flight && (!$flights->firstWhere('id', $package->assigned_flight)?->isActive || $flights->firstWhere('id', $package->assigned_flight)?->status == 'Canceled')) as $package)
                <tr class="border-b">
                    <td class="py-2 px-4 border">
                        <input type="checkbox" class="package-checkbox" value="{{ $package->id }}">
                        {{ $package->reference }}
                    </td>
                    <td class="py-2 px-4 border">{{ $package->weight }} kg</td>
                    <td class="py-2 px-4 border">
                        @php
                            $flight = is_numeric($package->assigned_flight) 
                                ? $flights->firstWhere('id', $package->assigned_flight) 
                                : null;
                        @endphp

                        @if($flight)
                            Flight {{ $flight->id }} - {{ $flight->departure_time }} to {{ $flight->arrivalAirport->name ?? 'Unknown' }}
                        @else
                            Flight ID: {{ $package->assigned_flight }}
                        @endif
                    </td>
                    <td class="py-2 px-4 border">
                        <button onclick="selectPackageAndOpenFlightModal({{ $package->id }})" 
                            class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-700 transition">
                            Re-assign Flight
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-2 px-4 border text-center text-gray-600">No packages to be reassigned.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Assigned Packages Table -->
    <h2 class="text-xl font-semibold mb-2">Assigned Packages</h2>
    <div class="overflow-x-auto mb-8">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">Package Reference</th>
                    <th class="py-2 px-4 border">Weight</th>
                    <th class="py-2 px-4 border">Assigned Flight</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages->filter(fn($package) => $package->assigned_flight && $flights->firstWhere('id', $package->assigned_flight)?->isActive && $flights->firstWhere('id', $package->assigned_flight)?->status !== 'Canceled') as $package)
                <tr class="border-b">
                    <td class="py-2 px-4 border">{{ $package->reference }}</td>
                    <td class="py-2 px-4 border">{{ $package->weight }} kg</td>
                    <td class="py-2 px-4 border">
                        @php
                            $flight = is_numeric($package->assigned_flight) 
                                ? $flights->firstWhere('id', $package->assigned_flight) 
                                : null;
                        @endphp

                        @if($flight)
                            Flight {{ $flight->id }} - {{ $flight->departure_time }} to {{ $flight->arrivalAirport->name ?? 'Unknown' }}
                        @else
                            Flight ID: {{ $package->assigned_flight }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-2 px-4 border text-center text-gray-600">No assigned packages available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="packageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-1/2 max-w-lg shadow-lg">
            <button class="absolute top-4 right-4 text-gray-600 hover:text-red-500 text-xl" onclick="closeModal()">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Packages for Flight <span id="flightId"></span></h2>
            <ul id="packageList" class="max-h-60 overflow-y-auto border p-3 rounded bg-gray-100 space-y-2"></ul>
            <div class="text-right mt-4">
                <button onclick="closeModal()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition">Close</button>
            </div>
        </div>
    </div>

    <!-- Flight Selection Modal -->
    <div id="flightModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-1/2 max-w-lg shadow-lg">
            <button class="absolute top-4 right-4 text-gray-600 hover:text-red-500 text-xl" onclick="closeFlightModal()">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Select a Flight</h2>
            <ul id="flightList" class="max-h-60 overflow-y-auto border p-3 rounded bg-gray-100 space-y-2">
            </ul>
            <div class="text-right mt-4">
                <button onclick="closeFlightModal()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition">Cancel</button>
            </div>
        </div>
    </div>

    <script>
    let selectedPackageIds = [];

    function openFlightModal() {
        selectedPackageIds = Array.from(document.querySelectorAll('.package-checkbox:checked')).map(cb => cb.value);

        if (selectedPackageIds.length === 0) {
            alert("Please select at least one package.");
            return;
        }

        // Validate that all selected packages have the same next movement
        const nextMovements = new Set();
        selectedPackageIds.forEach(packageId => {
            const packageRow = document.querySelector(`.package-checkbox[value="${packageId}"]`).closest('tr');
            const nextMovementCell = packageRow.querySelector('td:nth-child(3)');
            const nextMovement = nextMovementCell.textContent.trim();
            nextMovements.add(nextMovement);
        });

        if (nextMovements.size > 1) {
            alert("You cannot select packages with different next movements.");
            return;
        }

        // Filter flights based on the selected packages
        const filteredFlights = @json($flights).filter(flight => {
            return selectedPackageIds.every(packageId => {
                const package = @json($packages).find(pkg => pkg.id == packageId);
                const currentLocation = package?.current_location_id;
                const nextMovement = package?.next_movement_id; // Use precomputed next movement ID
                const routerEdge = @json($routerEdges).find(edge => edge.id == flight.router_edge_id);
                console.log("Package:", package);
                console.log("Current Location:", currentLocation);
                console.log("Next Movement:", nextMovement);
                console.log("Router Edge:", routerEdge);
                console.log("Flight:", flight);
                return currentLocation && nextMovement && routerEdge &&
                    (routerEdge.origin_node === currentLocation || routerEdge.destination_node === currentLocation) &&
                    (routerEdge.origin_node === nextMovement || routerEdge.destination_node === nextMovement) &&
                    flight.status !== 'Canceled';
            });
        });
        console.log("Filtered Flights:", filteredFlights);

        const flightList = document.getElementById('flightList');
        flightList.innerHTML = ''; // Clear previous list

        if (filteredFlights.length === 0) {
            flightList.innerHTML = '<li class="text-gray-600">No flights available for the selected packages.</li>';
        } else {
            filteredFlights.forEach(flight => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <button onclick="assignFlightToSelectedPackages(${flight.id})" 
                        class="block w-full text-left px-4 py-2 bg-white hover:bg-gray-200 rounded shadow-sm">
                        Flight ${flight.id} - ${flight.departure_time} to ${flight.arrivalAirport?.name ?? 'Unknown'}
                    </button>
                `;
                flightList.appendChild(li);
            });
        }

        document.getElementById('flightModal').classList.remove('hidden'); // Ensure the modal is displayed
    }

    function closeFlightModal() {
        document.getElementById('flightModal').classList.add('hidden');
        selectedPackageIds = [];
    }

    function showPackages(flightId) {
        document.getElementById('flightId').innerText = flightId;
        let packageList = document.getElementById('packageList');
        packageList.innerHTML = ''; // Clear previous list

        let packages = [];

        @foreach($flights as $flight)
            if ({{ $flight->id }} === flightId) {
                packages = {!! json_encode($flight->arrivalLocation?->packages ?? $flight->departureLocation?->packages ?? []) !!};
            }
        @endforeach

        if (packages.length === 0) {
            packageList.innerHTML = '<li class="text-gray-600">No packages available</li>';
        } else {
            packages.forEach(pkg => {
                let li = document.createElement('li');
                li.textContent = pkg.reference;
                li.classList.add("bg-white", "p-2", "rounded", "shadow-sm");
                packageList.appendChild(li);
            });
        }

        document.getElementById('packageModal').classList.remove("hidden");
    }

    function selectPackageAndOpenFlightModal(packageId) {
        // Keep the checkbox for the selected package checked
        const checkbox = document.querySelector(`.package-checkbox[value="${packageId}"]`);
        if (checkbox) {
            checkbox.checked = true;
        }

        // Pass the selected package ID to the modal
        selectedPackageIds = [packageId];

        // Validate that the selected package has a valid next movement
        const packageRow = document.querySelector(`.package-checkbox[value="${packageId}"]`).closest('tr');
        const nextMovementCell = packageRow.querySelector('td:nth-child(3)');
        const nextMovement = nextMovementCell.textContent.trim();

        if (!nextMovement || nextMovement === 'N/A') {
            alert("The selected package does not have a valid next movement.");
            return;
        }

        // Filter flights based on the selected package
        const filteredFlights = @json($flights).filter(flight => {
            const package = @json($packages).find(pkg => pkg.id == packageId);
            const currentLocation = package?.current_location_id;
            const nextMovement = package?.next_movement_id; // Use precomputed next movement ID
            const routerEdge = @json($routerEdges).find(edge => edge.id == flight.router_edge_id);
            // Fix: define arrive inside the filter function
            const arrive = flight.arrivalAirport?.name ?? 'Unknown';

            console.log("Package:", package);
            console.log("Current Location:", currentLocation);
            console.log("Next Movement:", nextMovement);
            console.log("Router Edge:", routerEdge);
            console.log("Flight:", flight);

            return currentLocation && nextMovement && routerEdge &&
                (routerEdge.origin_node === currentLocation || routerEdge.destination_node === currentLocation) &&
                (routerEdge.origin_node === nextMovement || routerEdge.destination_node === nextMovement) &&
                flight.status !== 'Canceled';
        });

        console.log("Filtered Flights:", filteredFlights);

        const flightList = document.getElementById('flightList');
        flightList.innerHTML = ''; // Clear previous list

        if (filteredFlights.length === 0) {
            flightList.innerHTML = '<li class="text-gray-600">No flights available for the selected package.</li>';
        } else {
            filteredFlights.forEach(flight => {
                // Fix: use arrive here, which is now defined above
                const arrive = flight.arrivalAirport?.name ?? 'Unknown';
                const li = document.createElement('li');
                li.innerHTML = `
                    <button onclick="assignFlightToSelectedPackages(${flight.id})" 
                        class="block w-full text-left px-4 py-2 bg-white hover:bg-gray-200 rounded shadow-sm">
                        Flight ${flight.id} - ${flight.departure_time} to ${arrive}
                    </button>
                `;
                flightList.appendChild(li);
            });
        }

        // Open the flight modal
        document.getElementById('flightModal').classList.remove('hidden');
    }

    function assignFlightToSelectedPackages(flightId) {
        if (selectedPackageIds.length === 0) {
            alert("No packages selected for assignment.");
            return;
        }

        // Calculate total weight of selected packages
        let totalWeight = 0;
        selectedPackageIds.forEach(packageId => {
            const packageRow = document.querySelector(`.package-checkbox[value="${packageId}"]`).closest('tr');
            const weightCell = packageRow.querySelector('td:nth-child(2)');
            const weight = parseFloat(weightCell.textContent.replace(' kg', ''));
            totalWeight += weight;
        });

        fetch(`{{ route('workspace.assign-flight') }}`, { // Use the correct route name
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ packageIds: selectedPackageIds, flightId, totalWeight })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(`Packages assigned to flight successfully! Total weight assigned: ${data.assignedWeight} kg`);

                // Remove successfully assigned packages from the table
                data.assignedPackageIds.forEach(packageId => {
                    const packageRow = document.querySelector(`.package-checkbox[value="${packageId}"]`).closest('tr');
                    packageRow.remove();
                });

                // Unselect all checkboxes
                document.querySelectorAll('.package-checkbox').forEach(cb => cb.checked = false);

                // Reload the page or update the UI as needed
                location.reload();
            } else {
                console.error("Backend error:", data); // Log backend error details
                alert("Failed to assign packages to flight: " + data.message);
            }
        })
        .catch(error => {
            console.error("Fetch error:", error); // Log fetch error details
            alert("An unexpected error occurred while assigning the packages.");
        });
    }

    function closeModal() {
        document.getElementById('packageModal').classList.add("hidden");
    }

    function sendToDistributionCenter(packageId) {
        fetch(`/send-to-distribution-center`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ packageId })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert("Package sent to distribution center successfully!");
                location.reload();
            } else {
                alert("Failed to send package to distribution center: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An unexpected error occurred.");
        });
    }
    </script>
</body>
</html>
</x-sidebar-airport>
</x-app-layout>