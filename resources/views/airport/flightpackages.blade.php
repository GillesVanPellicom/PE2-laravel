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

    <h1 class="text-2xl font-bold mb-4">Incoming Flights</h1>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">Flight Number</th>
                    <th class="py-2 px-4 border">Departure Time</th>
                    <th class="py-2 px-4 border">Departure Place</th>
                    <th class="py-2 px-4 border">Flight Duration (min)</th>
                    <th class="py-2 px-4 border">Estimated Arrival Time</th>
                    <th class="py-2 px-4 border">Arrival Place</th>
                    <th class="py-2 px-4 border">Status</th>
                    <th class="py-2 px-4 border">Packages</th>
                </tr>
            </thead>
            <tbody>
                @foreach($flights as $flight)
                    @if($flight->arrive_location_id == 1)
                    <tr class="border-b">
                        <td class="py-2 px-4 border">{{ $flight->id }}</td>
                        <td class="py-2 px-4 border">{{ $flight->departure_time }}</td>
                        <td class="py-2 px-4 border">{{ $flight->departureAirport->name }}</td>
                        <td class="py-2 px-4 border">{{ $flight->time_flight_minutes }}</td>
                        <td class="py-2 px-4 border">{{ \Carbon\Carbon::parse($flight->departure_time)->addMinutes($flight->time_flight_minutes)->format('H:i') }}</td>
                        <td class="py-2 px-4 border">{{ $flight->arrivalAirport->name }}</td>
                        <td class="py-2 px-4 border">{{ $flight->status }}</td>
                        <td class="py-2 px-4 border">
                            <button onclick="showPackages({{ $flight->id }})" 
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                View Packages ({{ count($flight->arrivalLocation->packages ?? []) }})
                            </button>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <h1 class="text-2xl font-bold mt-8 mb-4">Outgoing Flights</h1>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">Flight Number</th>
                    <th class="py-2 px-4 border">Departure Time</th>
                    <th class="py-2 px-4 border">Departure Place</th>
                    <th class="py-2 px-4 border">Flight Duration (min)</th>
                    <th class="py-2 px-4 border">Estimated Arrival Time</th>
                    <th class="py-2 px-4 border">Arrival Place</th>
                    <th class="py-2 px-4 border">Status</th>
                    <th class="py-2 px-4 border">Packages</th>
                </tr>
            </thead>
            <tbody>
                @foreach($flights as $flight)
                    @if($flight->depart_location_id == 1)
                    <tr class="border-b">
                        <td class="py-2 px-4 border">{{ $flight->id }}</td>
                        <td class="py-2 px-4 border">{{ $flight->departure_time }}</td>
                        <td class="py-2 px-4 border">{{ $flight->departureAirport->name }}</td>
                        <td class="py-2 px-4 border">{{ $flight->time_flight_minutes }}</td>
                        <td class="py-2 px-4 border">{{ \Carbon\Carbon::parse($flight->departure_time)->addMinutes($flight->time_flight_minutes)->format('H:i') }}</td>
                        <td class="py-2 px-4 border">{{ $flight->arrivalAirport->name }}</td>
                        <td class="py-2 px-4 border">{{ $flight->status }}</td>
                        <td class="py-2 px-4 border">
                            <button onclick="showPackages({{ $flight->id }})" 
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                View Packages ({{ count($flight->departureLocation->packages ?? []) }})
                            </button>
                        </td>
                    </tr>
                    @endif
                @endforeach
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

    <script>
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

        function closeModal() {
            document.getElementById('packageModal').classList.add("hidden");
        }
    </script>

</body>
</html>
</x-sidebar-airport>
</x-app-layout>