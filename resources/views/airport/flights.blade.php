<x-app-layout>
    @section('title', 'Airport Flight Packages')
<x-sidebar-airport>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-2xl font-bold mb-4">Incoming Flights</h1>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">Flight Number</th>

                    <th class="py-2 px-4 border">Departure Place</th>
                    <th class="py-2 px-4 border">Flight Duration (min)</th>
                    <th class="py-2 px-4 border">Estimated Arrival Time</th>

                    <th class="py-2 px-4 border">Gate</th>
                    <th class="py-2 px-4 border">Status</th>
                    <th class="py-2 px-4 border">Packages</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $employee = auth()->user()->employee;
                    $contract = $employee->contracts()->latest('start_date')->first();
                    $employeeLocationId = $contract ? $contract->location_id : null;
                @endphp
                @foreach($flights as $flight)

                    @if($flight->arrive_location_id == $employeeLocationId)
                    <tr class="border-b">
                        <td class="py-2 px-4 border">{{$flight->id}}</td>

                        <td class="py-2 px-4 border">{{$flight->departureAirport->name ?? 'Unknown Departure Airport'}}</td>
                        <td class="py-2 px-4 border">{{$flight->time_flight_minutes}}</td>
                        <td class="py-2 px-4 border">{{$flight->arrival_time}}</td>

                        <td class="py-2 px-4 border">{{$flight->gate ?? 'Unknown Gate'}}</td>
                        <td class="py-2 px-4 border">
                            @if($flight->status == 'On Time')
                                {{$flight->status}}
                            @elseif($flight->status == 'Delayed')
                                {{$flight->status}} (Delayed by {{$flight->delayed_minutes}} minutes)
                            @elseif($flight->status == 'Canceled')
                                {{$flight->status}}
                            @endif
                        </td>
                        <td class="py-2 px-4 border">
                            <button onclick="showPackages({{ $flight->id }})"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                View Packages ({{ \App\Models\Package::where('assigned_flight', $flight->id)->count() }})
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

                    <th class="py-2 px-4 border">Flight Duration (min)</th>

                    <th class="py-2 px-4 border">Arrival Place</th>
                    <th class="py-2 px-4 border">Gate</th>
                    <th class="py-2 px-4 border">Status</th>
                    <th class="py-2 px-4 border">Packages</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $employee = auth()->user()->employee;
                    $contract = $employee->contracts()->latest('start_date')->first();
                    $employeeLocationId = $contract ? $contract->location_id : null;
                @endphp
                @foreach($flights as $flight)

                    @if($flight->depart_location_id==$employeeLocationId)
                    <tr class="border-b">
                        <td class="py-2 px-4 border">{{$flight->id}}</td>
                        <td class="py-2 px-4 border">{{$flight->departure_time}}</td>

                        <td class="py-2 px-4 border">{{$flight->time_flight_minutes}}</td>

                        <td class="py-2 px-4 border">{{$flight->arrivalAirport->name ?? 'Unknown Arrival Airport'}}</td>
                        <td class="py-2 px-4 border">{{$flight->gate ?? 'Unknown Gate'}}</td>
                        <td class="py-2 px-4 border">
                            @if($flight->status == 'On Time')
                                {{$flight->status}}
                            @elseif($flight->status == 'Delayed')
                                {{$flight->status}} (Delayed by {{$flight->delayed_minutes}} minutes)
                            @elseif($flight->status == 'Canceled')
                                {{$flight->status}}
                            @endif
                        </td>
                        <td class="py-2 px-4 border">
                            <button onclick="showPackages({{ $flight->id }})"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                View Packages ({{ \App\Models\Package::where('assigned_flight', $flight->id)->count() }})
                            </button>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
    const flightPackages = @json($flights->mapWithKeys(fn($flight) => [
        $flight->id => \App\Models\Package::where('assigned_flight', $flight->id)->get()->map(fn($pkg) => $pkg->reference)->toArray()
    ]));

    function showPackages(flightId) {
        document.getElementById('flightId').innerText = flightId;
        let packageList = document.getElementById('packageList');
        packageList.innerHTML = ''; // Clear previous list

        const packages = flightPackages[flightId] || [];

        if (packages.length === 0) {
            packageList.innerHTML = '<li class="text-gray-600">No packages assigned to this flight</li>';
        } else {
            packages.forEach(pkg => {
                let li = document.createElement('li');
                li.textContent = pkg;
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
</body>
</html>
</x-sidebar-airport>
</x-app-layout>
