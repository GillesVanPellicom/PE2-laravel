<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <a href="{{ route('airports') }}">airports</a>
    <h1>incomming</h1>
    <table>
        <thead>
            <tr>
                <th>flightnumber</th>
                <th>departure time</th>
                <th>departure place</th>
                <th>flight duration</th>
                <th>estimated arrival time</th>
                <th>arrival place</th>
                <th>status<th>
            </tr>
        </thead>
        <tbody>
            @foreach($flights as $flight)
            @if($flight->arrive_location_id == 1)
                <tr>
                    <td>{{$flight->id}}</td>
                    <td>{{$flight->departure_time}}</td>
                    <td>{{$flight->departureAirport->name}}</td>
                    <td>{{$flight->time_flight_minutes}}</td>
                    <td>{{$flight->arrival_time}}</td>
                    <td>{{$flight->arrivalAirport->name}}</td>
                    <td>{{$flight->status}}</td>  
                </tr>
            @endif 
            @endforeach
        </tbody>
    </table>
    <h1>outgoing</h1>
    <table>
        <thead>
            <tr>
                <th>flightnumber</th>
                <th>departure time</th>
                <th>departure place</th>
                <th>flight duration</th>
                <th>estimated arrival time</th>
                <th>arrival place</th>
                <th>status<th>
            </tr>
        </thead>
        <tbody>
            @foreach($flights as $flight)
            @if($flight->depart_location_id == 1)
                <tr>
                
                    <td>{{$flight->id}}</td>
                    <td>{{$flight->departure_time}}</td>
                    <td>{{$flight->departureAirport->name}}</td>
                    <td>{{$flight->time_flight_minutes}}</td>
                    <td>{{$flight->arrival_time}}</td>
                    <td>{{$flight->arrivalAirport->name}}</td>
                    <td>{{$flight->status}}</td>  
                
                </tr>
            @endif 
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('flightcreate') }}">Add flight</a>
    <form>

    </form>
</body>
</html>