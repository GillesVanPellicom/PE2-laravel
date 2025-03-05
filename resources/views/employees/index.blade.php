<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
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

    <div>
        <table>
            <thead>
                <tr>
                    <th>id</th>
                    <th>name</th>
                    <th>firstname</th>
                    <th>email</th>
                    <th>birthdate</th>
                    <th>date of hire</th>
                    <th>vacation days</th>
                    <!--<th>actions</th>-->
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    <tr>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->firstname }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->birthdate }}</td>
                        <td>{{ $employee->hire_date }}</td>
                        <td>{{ $employee->vacation_days }}</td>
                        <!--<td>
                            <a href="{/{ route('employees.edit', $employee->id) }}">edit</a>
                            <form method="post" action="{/{ route('employees.destroy', $employee->id) }}">
                                @/csrf
                                @/method('DELETE')
                                <button type="submit">delete</button>
                            </form>
                        </td>-->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>