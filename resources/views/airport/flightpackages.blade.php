<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Flight Packages</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid black; text-align: left; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 10% auto; padding: 20px; width: 50%; border-radius: 10px; }
        .close { float: right; font-size: 20px; cursor: pointer; }
    </style>
</head>
<body>

    <h1>Incoming Flights</h1>
    <table>
        <thead>
            <tr>
                <th>Flight Number</th>
                <th>Departure Time</th>
                <th>Departure Place</th>
                <th>Flight Duration (min)</th>
                <th>Estimated Arrival Time</th>
                <th>Arrival Place</th>
                <th>Status</th>
                <th>Packages</th>
            </tr>
        </thead>
        <tbody>
            @foreach($flights as $flight)
                @if($flight->arrive_location_id == 1)
                <tr>
                    <td>{{ $flight->id }}</td>
                    <td>{{ $flight->departure_time }}</td>
                    <td>{{ $flight->departureLocation->description }}</td>
                    <td>{{ $flight->time_flight_minutes }}</td>
                    <td>{{ \Carbon\Carbon::parse($flight->departure_time)->addMinutes($flight->time_flight_minutes)->format('H:i') }}</td>
                    <td>{{ $flight->arrivalLocation->description }}</td>
                    <td>{{ $flight->status }}</td>
                    <td>
                        <button onclick="showPackages({{ $flight->id }})">
                            View Packages ({{ count($flight->arrivalLocation->packages ?? []) }})
                        </button>
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <h1>Outgoing Flights</h1>
    <table>
        <thead>
            <tr>
                <th>Flight Number</th>
                <th>Departure Time</th>
                <th>Departure Place</th>
                <th>Flight Duration (min)</th>
                <th>Estimated Arrival Time</th>
                <th>Arrival Place</th>
                <th>Status</th>
                <th>Packages</th>
            </tr>
        </thead>
        <tbody>
            @foreach($flights as $flight)
                @if($flight->depart_location_id == 1)
                <tr>
                    <td>{{ $flight->id }}</td>
                    <td>{{ $flight->departure_time }}</td>
                    <td>{{ $flight->departureLocation->description }}</td>
                    <td>{{ $flight->time_flight_minutes }}</td>
                    <td>{{ \Carbon\Carbon::parse($flight->departure_time)->addMinutes($flight->time_flight_minutes)->format('H:i') }}</td>
                    <td>{{ $flight->arrivalLocation->description }}</td>
                    <td>{{ $flight->status }}</td>
                    <td>
                        <button onclick="showPackages({{ $flight->id }})">
                            View Packages ({{ count($flight->departureLocation->packages ?? []) }})
                        </button>
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Modal for showing package details -->
    <div id="packageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Packages for Flight <span id="flightId"></span></h2>
            <ul id="packageList"></ul>
        </div>
    </div>

    <script>
        function showPackages(flightId) {
            document.getElementById('flightId').innerText = flightId;
            let packageList = document.getElementById('packageList');
            packageList.innerHTML = ''; // Clear previous list

            @foreach($flights as $flight)
                if ({{ $flight->id }} === flightId) {
                    let packages = {!! json_encode($flight->departureLocation->packages ?? $flight->arrivalLocation->packages ?? []) !!};
                    if (packages.length === 0) {
                        packageList.innerHTML = '<li>No packages</li>';
                    } else {
                        packages.forEach(pkg => {
                            let li = document.createElement('li');
                            li.textContent = pkg.reference;
                            packageList.appendChild(li);
                        });
                    }
                }
            @endforeach

            document.getElementById('packageModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('packageModal').style.display = 'none';
        }
    </script>

</body>
</html>
