<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Airport</h1>
    <a href="{{ route('contract') }}">Contract</a>
    <br>
    <a href="{{ route('flights') }}">Flights</a>
    <br>
    <a href="{{ route('packages') }}">Packages</a>
    <br/>
    <tbody>
        @foreach($airports as $airport)
        <tr>
            <td>{{$airport->location_id}}</td>
            <td>{{$airport->name}}</td>
        </tr>
        <br/>
        @endforeach
    </tbody>
</body>
</html>