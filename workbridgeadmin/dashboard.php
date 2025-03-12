<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('config.php'); // Include your database connection

$sql = "
    SELECT
        (SELECT SUM(total_applicants) FROM applicants) AS sum_applicants,
        (SELECT SUM(total_employers) FROM employers) AS sum_employers,
        (SELECT SUM(total_applicants) FROM applicants) + (SELECT SUM(total_employers) FROM employers) AS total_sum
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $applicants = $row['sum_applicants'];
    $employers = $row['sum_employers'];
    $total_users = $row['total_sum'];
} else {
    // Default values if no data is found
    $applicants = 0;
    $employers = 0;
    $total_users = 0;
}



// Function to fetch data from the database based on the timeframe
function fetchData($conn, $table) {
    $sql = "SELECT * FROM $table";
    $result = $conn->query($sql);
    $data = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

$weekly_data = fetchData($conn, 'weekly_data');
$monthly_data = fetchData($conn, 'monthly_data');
$yearly_data = fetchData($conn, 'yearly_data');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="sidebar">
    <div class="logo">
        <img src="login_logo.png" alt="WorkBridge Logo">
    </div>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="employers.php">Employers</a></li>
        <li><a href="applicants.php">Applicants</a></li>
        <li><a href="logs.php">User Logs</a></li>
        <li><a href="users.php">User Accounts</a></li>
    </ul>
</div>

    <div class="main-content">
        <div class="header">
            <h1>Hello, <?php echo $_SESSION['username']; ?>!</h1>
        </div>
        <div class="summary-boxes">
    <div class="box">
        <h2>Total Users</h2>
        <p><?php echo $total_users; ?></p>
    </div>
    <div class="box">
        <h2>Total Employers</h2>
        <p><?php echo $employers; ?></p>
    </div>
    <div class="box">
        <h2>Total Applicants</h2>
        <p><?php echo $applicants; ?></p>
    </div>
</div>

</div>

</div>

        </div>
        <div class="graph-container">
            <select id="timeframe">
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
            <canvas id="myChart"></canvas>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var weeklyData = <?php echo json_encode($weekly_data); ?>;
        var monthlyData = <?php echo json_encode($monthly_data); ?>;
        var yearlyData = <?php echo json_encode($yearly_data); ?>;

        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'], // Default to weekly labels
                datasets: [
                    {
                        label: 'Users',
                        data: weeklyData.map(item => item.users), // Default to weekly data
                        borderColor: 'rgba(26, 188, 156, 1)',
                        borderWidth: 2,
                        fill: false,
                    },
                    {
                        label: 'Employers',
                        data: weeklyData.map(item => item.employers), // Default to weekly data
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 2,
                        fill: false,
                    },
                    {
                        label: 'Applicants',
                        data: weeklyData.map(item => item.applicants), // Default to weekly data
                        borderColor: 'rgba(231, 76, 60, 1)',
                        borderWidth: 2,
                        fill: false,
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 10
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 4,
                        backgroundColor: '#fff',
                        borderWidth: 2,
                        hoverRadius: 6,
                        hoverBorderWidth: 2
                    }
                }
            }
        });

        document.getElementById('timeframe').addEventListener('change', function() {
            var timeframe = this.value;
            updateChartData(timeframe);
        });

        function updateChartData(timeframe) {
            var data = {
                labels: [],
                datasets: [
                    {
                        label: 'Users',
                        data: [],
                        borderColor: 'rgba(26, 188, 156, 1)',
                        borderWidth: 2,
                        fill: false,
                    },
                    {
                        label: 'Employers',
                        data: [],
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 2,
                        fill: false,
                    },
                    {
                        label: 'Applicants',
                        data: [],
                        borderColor: 'rgba(231, 76, 60, 1)',
                        borderWidth: 2,
                        fill: false,
                    }
                ]
            };

            if (timeframe === 'weekly') {
                data.labels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                data.datasets[0].data = weeklyData.map(item => item.users);
                data.datasets[1].data = weeklyData.map(item => item.employers);
                data.datasets[2].data = weeklyData.map(item => item.applicants);
            } else if (timeframe === 'monthly') {
                data.labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                data.datasets[0].data = monthlyData.map(item => item.users);
                data.datasets[1].data = monthlyData.map(item => item.employers);
                data.datasets[2].data = monthlyData.map(item => item.applicants);
            } else if (timeframe === 'yearly') {
                data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                data.datasets[0].data = yearlyData.map(item => item.users);
                data.datasets[1].data = yearlyData.map(item => item.employers);
                data.datasets[2].data = yearlyData.map(item => item.applicants);
            }

            myChart.data = data;
            myChart.update();
        }
    </script>
</body>
</html>
