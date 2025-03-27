<x-app-layout>
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
                    <th class="py-2 px-4 border">Departure Time</th>
                    <th class="py-2 px-4 border">Departure Place</th>
                    <th class="py-2 px-4 border">Flight Duration (min)</th>
                    <th class="py-2 px-4 border">Estimated Arrival Time</th>
                    <th class="py-2 px-4 border">Arrival Place</th>
                    <th class="py-2 px-4 border">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($flights as $flight)
                @if($flight->arrive_location_id == 1)
                    <tr class="border-b">
                        <td class="py-2 px-4 border">{{$flight->id}}</td>
                        <td class="py-2 px-4 border">{{$flight->departure_time}}</td>
                        <td class="py-2 px-4 border">{{$flight->departureAirport->name ?? 'Unknown Departure Airport'}}</td>
                        <td class="py-2 px-4 border">{{$flight->time_flight_minutes}}</td>
                        <td class="py-2 px-4 border">{{$flight->arrival_time}}</td>
                        <td class="py-2 px-4 border">{{$flight->arrivalAirport->name ?? 'Unknown Arrival Airport'}}</td>
                        <td class="py-2 px-4 border">
                            @if($flight->status == 'On Time')
                                {{$flight->status}}
                            @elseif($flight->status == 'Delayed')
                                {{$flight->status}} (Delayed by {{$flight->delayed_minutes}} minutes)
                            @elseif($flight->status == 'Canceled')
                                {{$flight->status}}
                            @endif
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
                </tr>
            </thead>
            <tbody>
                @foreach($flights as $flight)
                @if($flight->depart_location_id == 1)
                    <tr class="border-b">
                        <td class="py-2 px-4 border">{{$flight->id}}</td>
                        <td class="py-2 px-4 border">{{$flight->departure_time}}</td>
                        <td class="py-2 px-4 border">{{$flight->departureAirport->name ?? 'Unknown Departure Airport'}}</td>
                        <td class="py-2 px-4 border">{{$flight->time_flight_minutes}}</td>
                        <td class="py-2 px-4 border">{{$flight->arrival_time}}</td>
                        <td class="py-2 px-4 border">{{$flight->arrivalAirport->name ?? 'Unknown Arrival Airport'}}</td>
                        <td class="py-2 px-4 border">
                            @if($flight->status == 'On Time')
                                {{$flight->status}}
                            @elseif($flight->status == 'Delayed')
                                {{$flight->status}} (Delayed by {{$flight->delayed_minutes}} minutes)
                            @elseif($flight->status == 'Canceled')
                                {{$flight->status}} (Flight has been canceled)
                            @endif
                        </td>
                    </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
</x-sidebar-airport>
</x-app-layout>