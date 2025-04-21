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

    <h1 class="text-2xl font-bold mb-4">Packages at Your Location</h1>

    <!-- Unassigned Packages Table -->
    <h2 class="text-xl font-semibold mb-2">Unassigned Packages</h2>
    <div class="overflow-x-auto mb-8">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">Package Reference</th>
                    <th class="py-2 px-4 border">Weight</th>
                    <th class="py-2 px-4 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages->filter(fn($package) => !$package->assigned_flight) as $package)
                <tr class="border-b">
                    <td class="py-2 px-4 border">{{ $package->reference }}</td>
                    <td class="py-2 px-4 border">{{ $package->weight }} kg</td>
                    <td class="py-2 px-4 border">
                        <button onclick="openFlightModal({{ $package->id }})" 
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                            Assign to Flight
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-2 px-4 border text-center text-gray-600">No unassigned packages available.</td>
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
                    <th class="py-2 px-4 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages->filter(fn($package) => $package->assigned_flight && $flights->firstWhere('id', $package->assigned_flight)?->status === 'Canceled') as $package)
                <tr class="border-b">
                    <td class="py-2 px-4 border">{{ $package->reference }}</td>
                    <td class="py-2 px-4 border">{{ $package->weight }} kg</td>
                    <td class="py-2 px-4 border">
                        <button onclick="openFlightModal({{ $package->id }})" 
                            class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-700 transition">
                            Re-assign Flight
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-2 px-4 border text-center text-gray-600">No packages to be reassigned.</td>
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
                @forelse($packages->filter(fn($package) => $package->assigned_flight && $flights->firstWhere('id', $package->assigned_flight)?->status !== 'Canceled') as $package)
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
                @foreach($flights as $flight)
                    <li>
                        <button onclick="assignFlight(selectedPackageId, {{ $flight->id }})" 
                            class="block w-full text-left px-4 py-2 bg-white hover:bg-gray-200 rounded shadow-sm">
                            Flight {{ $flight->id }} - {{ $flight->departure_time }} to {{ $flight->arrivalAirport->name ?? 'Unknown' }}
                        </button>
                    </li>
                @endforeach
            </ul>
            <div class="text-right mt-4">
                <button onclick="closeFlightModal()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 transition">Cancel</button>
            </div>
        </div>
    </div>

    <script>
    let selectedPackageId = null;

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

    function openFlightModal(packageId) {
        selectedPackageId = packageId;
        document.getElementById('flightModal').classList.remove('hidden');
    }

    function closeFlightModal() {
        document.getElementById('flightModal').classList.add('hidden');
        selectedPackageId = null;
    }

    function assignFlight(packageId, flightId) {
        fetch(`/assign-flight`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ packageId, flightId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Package assigned to flight successfully!");
                location.reload();
            } else {
                // Display the error message from the backend
                alert("Failed to assign package to flight: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An unexpected error occurred while assigning the package.");
        });
    }

    function closeModal() {
        document.getElementById('packageModal').classList.add("hidden");
    }
    </script>

</body>
</html>
</x-sidebar-airport>
</x-app-layout>