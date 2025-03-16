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
    <a href="{{ route('auth.login') }}">Login</a>
    <br>
    <a href="{{ route('auth.register') }}">Register</a>
    <br>
</div>
</body>
</html>