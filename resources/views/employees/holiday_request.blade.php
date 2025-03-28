<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Vacation Requests</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

    <h1>All Vacation Requests</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee ID</th>
                <th>Vacation Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Day Type</th> <!-- Add Day Type column -->
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vacations as $vacation)
                <tr>
                    <td>{{ $vacation->vacation_id }}</td>
                    <td>{{ $vacation->employee_id }}</td>
                    <td>{{ $vacation->vacation_type }}</td>
                    <td>{{ $vacation->start_date }}</td>
                    <td>{{ $vacation->end_date }}</td>
                    <td>{{ $vacation->day_type }}</td> <!-- Display Day Type -->
                    <td>{{ $vacation->approve_status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($vacations->isEmpty())
        <p>No vacation requests found.</p>
    @endif

</body>
</html>

