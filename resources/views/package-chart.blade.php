<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Aantal Beschikbare Pakketten per Dag</h1>

    <!-- Canvas element waar de grafiek wordt getekend -->
    <div style="width: 50%; margin: auto;">
        <canvas id="packageChart"></canvas>
    </div>
    

    <script>
        var ctx = document.getElementById('packageChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($day),  // Maanden op de X-as
                datasets: [{
                    label: 'Aantal Pakketten',
                    data: {!! json_encode($counts) !!},  // Aantal pakketten op de Y-as
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
