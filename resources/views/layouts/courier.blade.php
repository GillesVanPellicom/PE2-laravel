<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{ $title ?? 'Courier' }} </title>
    <link rel="icon" type="image/x-icon" href="{{ $icon ?? asset('ico/courier_icon.ico') }}">
    @vite('resources/css/app.css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
</head>

<body class="flex flex-col h-screen text-center pt-20 pb-16">
    <header class="fixed top-0 left-0 w-full bg-gray-800 text-white text-center py-4 text-xl font-bold z-50">
        ShipCompany
        @if (!request()->routeIs('courier'))
            <a href="{{ route('workspace.courier.logout') }}"
                class="absolute right-5 top-1/2 transform -translate-y-1/2 text-white text-2xl hover:text-black">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        @endif
    </header>

    {{ $slot }}

    <!-- These should only appear if the logged in user is a courier -->
    <nav class="fixed bottom-0 left-0 w-full bg-gray-800 flex justify-around py-3">
        <a href="{{ route('workspace.courier.route') }}" class="text-white text-3xl hover:text-black">
            <i class="fas fa-map"></i>
        </a>
        <a href="{{ route('workspace.courier.packages') }}" class="text-white text-3xl hover:text-black">
            <i class="fas fa-box"></i>
        </a>
        <a href="{{ route('workspace.courier.scan') }}" class="text-white text-3xl hover:text-black">
            <i class="fa-solid fa-qrcode"></i>
        </a>
    </nav>
</body>

</html>
