<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Homepage</title>
</head>
<body>
<div>
    <h1>Homepage</h1>
    @if (Auth::check())
        <p>You are logged in.</p>
        <a href="{{ route('customers') }}">Customer Page</a>
        <br>
        <form action="{{ route('auth.logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @else
        <p>You are not logged in.</p>
        <a href="{{ route('auth.login') }}">Login</a>
        <br>
        <a href="{{ route('auth.register') }}">Register</a>
        <br>
    @endif
</div>
</body>
</html>