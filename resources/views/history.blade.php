<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/colors.css') }}" rel="stylesheet">
    <!-- Name font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Honk:MORF@13&display=swap" rel="stylesheet">
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #completionRateChart {
            max-width: 100%;
            height: auto;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    @include('layouts.navbar')
    @php
        use App\Category;
    @endphp

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <!-- Left Section -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Missed Deadlines</h5>
                        <ul>
                            <li>Deadline 1 (Missed)</li>
                            <li>Deadline 2 (Missed)</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Right Section -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Completed Deadlines</h5>
                        <ul>
                            <li>Deadline 3 (Completed)</li>
                            <li>Deadline 4 (Completed)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-5">
            <h3>Completion Chart</h3>
            <canvas id="completionRateChart"></canvas>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dummy data for completion rate
        var completionRateData = {
            labels: ["Completed", "Missed"],
            datasets: [{
                label: 'Completion Rate',
                data: [75, 25], // Dummy completion rate data (e.g., 75% completed, 25% missed)
                backgroundColor: [
                    'rgba(54, 162, 235, 0.5)', // Blue for completed
                    'rgba(255, 99, 132, 0.5)'  // Red for missed
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Draw completion rate chart
        var completionRateChart = new Chart(document.getElementById('completionRateChart'), {
            type: 'doughnut',
            data: completionRateData,
            options: {
                responsive: false,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: 'bottom'
                },
                title: {
                    display: false,
                    text: 'Completion Rate'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    </script>

</body>
</html>
