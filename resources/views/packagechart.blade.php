<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <form method="GET" action="{{ route('workspace.package.chart') }}" style="text-align: center; margin-bottom: 20px;">
        <label for="year">Selecteer een jaar:</label>
        <select name="year" id="year" onchange="this.form.submit()">
            @foreach ($availableYears as $year)
                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>

        <label for="group_by">Groeperen op:</label>
        <select name="group_by" id="group_by" onchange="this.form.submit()">
            <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>Per Dag</option>
            <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>Per Week</option>
            <option value="month" {{ $groupBy == 'month' ? 'selected' : '' }}>Per Maand</option>
        </select>
    </form>

    <h1>Aantal Inkomende Pakketten per Dag</h1>

    <div style="width: 50%; margin: auto;">
        <canvas id="packageChart"></canvas>
    </div>

    <h1>Aantal Uitkomende Pakketten per Dag</h1>

    <div style="width: 50%; margin: auto;">
        <canvas id="Outgoingpackage"></canvas>
    </div>



    <script>
        // Inkomende pakketten grafiek
        var ctxIn = document.getElementById('packageChart').getContext('2d');
        var incomingChart = new Chart(ctxIn, {
            type: 'line',
            data: {
                labels: @json($incomingDays),
                datasets: [{
                    label: 'Aantal Inkomende Pakketten',
                    data: @json($incomingCounts),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Periode'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Aantal Pakketten'
                        },
                        ticks: {
                            stepSize: 2
                        }
                    }
                }
            }
        });

        // Uitgaande pakketten grafiek
        var ctxOut = document.getElementById('Outgoingpackage').getContext('2d');
        var outgoingChart = new Chart(ctxOut, {
            type: 'line',
            data: {
                labels: @json($outgoingDays),
                datasets: [{
                    label: 'Aantal Uitgaande Pakketten',
                    data: @json($outgoingCounts),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Periode'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Aantal Pakketten'
                        },
                        ticks: {
                            stepSize: 2
                        }
                    }
                }
            }
        });

    </script>
</body>
</html>
