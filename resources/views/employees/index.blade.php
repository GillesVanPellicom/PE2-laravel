<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            background-color: #aaa;
        }
        .cent {
            width: 50%; /* or any fixed width */
            margin: 0 auto;
            text-align: center; /* Optional for centering text */
        }
    </style>
</head>
<body>
    <div class="cent">
    <h1>employees</h1>
    <div>
        <h1><a href="{{ route('employees.create') }}">create employee</a></h1>
    </div>

    @if (session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    <div>
        <table>
            <thead>
                <tr>
                    <th>id</th>
                    <th>last name</th>
                    <th>first name</th>
                    <th>email</th>
                    <th>phone number</th>
                    <th>birth date</th>
                    <th>address</th>
                    <th>nationality</th>
                    <th>city</th>
                    <th>country</th>
                    <th>leave balance</th>
                    <th>created at</th>
                    <th>updated at</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    <tr>
                        <td>{{ $employee->id }}</td>
                        <td>{{ $employee->last_name }}</td>
                        <td>{{ $employee->first_name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->phone_number }}</td>
                        <td>{{ $employee->birth_date }}</td>
                        <td>{{ $employee->address->street }} {{ $employee->address->house_number }} {{ $employee->address->bus_number }}</td>
                        <td>{{ $employee->nationality }}</td>
                        <td>{{ $employee->address->city->name }}</td>
                        <td>{{ $employee->address->city->country->country_name }}</td>
                        <td>{{ $employee->leave_balance }}</td>
                        <td>{{ $employee->created_at }}</td>
                        <td>{{ $employee->updated_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>