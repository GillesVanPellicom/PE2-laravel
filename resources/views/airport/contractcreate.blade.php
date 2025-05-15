<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <a href="{{ route('workspace.airports') }}">Airports</a>
    <h1>create contract</h1>
    <form method='post' action='{{route('workspace.contract.store')}}'>
        @csrf
        @method('post')
        <div>
            <label>airline</label>
            <input type="text" name="airline"/>
        </div>
        <div>
            <label>flight</label>
            <input type="number" name="flight"/>
        </div>
        <div>
            <label>weight</label>
            <input type="float" name="weight"/>
        </div>
        <div>
            <label>room</label>
            <input type="float" name="room"/>
        </div>
        <div>
            <input type="submit" value="save contract info"/>
        </div>

    </form>
</body>
</html>
