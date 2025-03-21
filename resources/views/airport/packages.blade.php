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
    <h2>Packages</h2>
    <ul>
        @foreach ($packages as $package)
            <li>{{ $package->name }} - {{ $package->description }}</li>
        @endforeach
    </ul>
</body>
</html>