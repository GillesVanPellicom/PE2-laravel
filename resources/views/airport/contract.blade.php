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
                <th>flight</th>
                <th>wheight available (kg)</th>
                <th>price (€)</th>
                <th>start date</th>
                <th>end date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contracts as $contract)
            <tr>
                <td>{{$contract->airline_id}}</td>
                <td>{{$contract->flight_id}}</td>
                <td>{{$contract->max_capacity}}</td>
                <td>{{$contract->price}}</td>
                <td>{{$contract->start_date}}</td>
                <td>{{$contract->end_date}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('contractcreate') }}">Add contract</a>
</body>
</html>