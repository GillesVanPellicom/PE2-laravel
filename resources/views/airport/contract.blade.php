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
    <a href="{{ route('airports') }}">Airports</a>
    <table>
        <thead>
            <tr>
                <th>airline</th>
                <th>#airplanes</th>
                <th>room/weight on airplanes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contracts as $contract)
            <tr>
                <td>{{$contract->airline}}</td>
                <td>{{$contract->flight}}</td>
                <td>{{$contract->weight}}</td>
                <td>{{$contract->room}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('contractcreate') }}">Add contract</a>
</body>
</html>