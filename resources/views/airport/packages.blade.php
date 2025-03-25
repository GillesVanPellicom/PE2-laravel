<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Packages at {{ $airport->name }}</title>
</head>
<body>
    <a href="{{ route('airports') }}">airports</a>
    <h2>Packages at {{ $airport->name }}</h2>
    <ul>
        @forelse ($packages as $package)
            <li>{{ $package->name }} - {{ $package->description }}</li>
        @empty
            <li>No packages currently at this airport.</li>
        @endforelse
    </ul>
</body>
</html>