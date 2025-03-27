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
    <a href="{{ route('contract') }}">Contracts</a>
    <br>
    <a href="{{ route('flights') }}">Flights</a>
    <br>
    <a href="{{ route('flightpackages') }}">Flight Packages</a>
    <br/>
</body>
</html>