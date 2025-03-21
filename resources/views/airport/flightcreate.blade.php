<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <a href="{{ route('airports') }}">Airports</a>
    <h1>create flight</h1>
    <form method='post' action='{{route('flight.store')}}'>
        @csrf
        @method('post')
        <div>
            <label>airplane_id</label>
            <input type="number" name="airplane_id"/>
        </div>
        <div>
            <label>departure_time</label>
            <input type="text" name="departure_time"/>
        </div>
        <div>
            <label>arrival_time</label>
            <input type="text" name="arrival_time"/>
        </div>
        <div>
            <label>departure_place</label>
            <input type="text" name="depart_location_id"/>
        </div>
        <div>
            <label>arrival_place</label>
            <input type="text" name="arrive_location_id"/>
        </div>
        <div>
            <label>number_of_packages</label>
            <input type="text" name="status"/>
        </div>
        <div>
            <input type="submit" value="save flight info"/>
        </div>

    </form>
</body>
</html>