<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
</head>
<body>
<div>
    <h1>Homepage</h1>
    <p>This is the homepage.</p>
    @auth
        <form action="{{ route('auth.logout') }}" method="POST" class="mb-6">
            @csrf
            <button type="submit" class=" text-white py-2 px-4 rounded-md">Sign Out</button>
        </form>
    @endauth
    @guest
    <a href="{{ route('auth.login') }}">Login</a>
    <br>
    <a href="{{ route('auth.register') }}">Register</a>
    <br>
    @endguest
</div>
</body>
</html>