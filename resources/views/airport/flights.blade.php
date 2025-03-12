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
    <h1>incomming</h1>
    <a href="{{ route('airports') }}">airports</a>
    <table>
        <thead>
            <tr>
                <th>flightnumber</th>
                <th>departure time</th>
                <th>departure place</th>
                <th>arrival time</th>
                <th>arrival place</th>
                <th>status<th>
            </tr>
        </thead>
        <tbody>
            @foreach($flights as $flight)
            <tr>
                @if($arrive_location_id = 1)
                    <td>{{$flight->flight_id}}</td>
                    <td>{{$flight->departure_time}}</td>
                    <td>{{$flight->depart_location_id}}</td>
                    <td>{{$flight->arrival_time}}</td>
                    <td>{{$flight->arrive_location_id}}</td>
                    <td>{{$flight->status}}</td>  
                @endif 
            </tr>
            @endforeach
        </tbody>
    </table>
    <h1>outgoing</h1>
    <a href="{{ route('airports') }}">airports</a>
    <table>
        <thead>
            <tr>
                <th>flightnumber</th>
                <th>departure time</th>
                <th>departure place</th>
                <th>arrival time</th>
                <th>arrival place</th>
                <th>status<th>
            </tr>
        </thead>
        <tbody>
            @foreach($flights as $flight)
            <tr>
                @if($depart_location_id = 1)
                    <td>{{$flight->flight_id}}</td>
                    <td>{{$flight->departure_time}}</td>
                    <td>{{$flight->depart_location_id}}</td>
                    <td>{{$flight->arrival_time}}</td>
                    <td>{{$flight->arrive_location_id}}</td>
                    <td>{{$flight->status}}</td>  
                @endif 
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('flightcreate') }}">Add flight</a>
    <form>

    </form>
</body>
</html>