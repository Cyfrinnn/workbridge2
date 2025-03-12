<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('config.php'); // Include your database connection

// Handle approval and denial actions
if (isset($_POST['action']) && isset($_POST['employer_id'])) {
    $employer_id = $_POST['employer_id'];
    $action = $_POST['action'];
    $new_status = $action === 'approve' ? 'Approved' : 'Denied';
    
    $conn->query("UPDATE employers SET status = '$new_status' WHERE id = $employer_id");
    header("Location: employers.php"); // Redirect to refresh the page
    exit();
}

// Fetch data for status boxes, treating NULL as Pending
$pending_count = $conn->query("SELECT COUNT(*) AS count FROM employers WHERE status IS NULL OR status = 'Pending'")->fetch_assoc()['count'];
$approved_count = $conn->query("SELECT COUNT(*) AS count FROM employers WHERE status = 'Approved'")->fetch_assoc()['count'];
$denied_count = $conn->query("SELECT COUNT(*) AS count FROM employers WHERE status = 'Denied'")->fetch_assoc()['count'];

// Fetch employer data
$employers = $conn->query("SELECT * FROM employers");

// Fetch unread notifications count
$unread_notifications_count = $conn->query("SELECT COUNT(*) AS count FROM notifications WHERE is_read = FALSE")->fetch_assoc()['count'];

// Insert notification when a new company is added
if (isset($_POST['add_employer'])) {
    $company_name = $_POST['company_name'];
    $email = $_POST['email'];
    $proofs = $_POST['proofs'];
    
    // Insert new employer data into the database
    $conn->query("INSERT INTO employers (company_name, email, proofs) VALUES ('$company_name', '$email', '$proofs')");
    
    // Insert a new notification message
    $conn->query("INSERT INTO notifications (message) VALUES ('New company added: $company_name')");
    
    header("Location: employers.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employers</title>
    <link rel="stylesheet" href="employers.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="login_logo.png" alt="WorkBridge Logo">
        </div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li class="active"><a href="employers.php">Employers</a></li>
            <li><a href="applicants.php">Applicants</a></li>
            <li><a href="logs.php">User Logs</a></li>
            <li><a href="users.php">User Accounts</a></li>
        </ul>
    </div>
    <div class="main-content">
        <header>
            <div class="header-content">
                <div class="header-buttons">
                    <!-- Display the unread notifications count -->
                    <button class="notification-button">&#128276; <span class="notification-count"><?php echo $unread_notifications_count; ?></span></button>
                    <button class="profile-button">&#128100;</button>
                </div>
            </div>
        </header>
        <div class="status-boxes">
            <div class="box pending">
                <h2>Pending</h2>
                <p><?php echo $pending_count; ?></p>
            </div>
            <div class="box approved">
                <h2>Approved</h2>
                <p><?php echo $approved_count; ?></p>
            </div>
            <div class="box denied">
                <h2>Denied</h2>
                <p><?php echo $denied_count; ?></p>
            </div>
        </div>
        <div class="search-filter">
            <div class="search-bar">
                <span class="icon">&#128269;</span>
                <input type="text" id="searchBar" placeholder="Search...">
            </div>
            <select id="statusFilter">
                <option value="all">All</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="denied">Denied</option>
            </select>
        </div>
        <div class="employers-table">
            <form method="post" id="actionForm">
                <input type="hidden" name="employer_id" id="employerId">
                <input type="hidden" name="action" id="actionType">
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Email Address</th>
                        <th>Proofs</th>
                        <th>Status</th>
                        <th>Approval</th>
                    </tr>
                </thead>
                <tbody id="employersTable">
                    <?php while($row = $employers->fetch_assoc()): ?>
                    <tr data-status="<?php echo strtolower($row['status'] ?? 'pending'); ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['company_name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo isset($row['proofs']) ? $row['proofs'] : 'N/A'; ?></td>
                        <td><?php echo $row['status'] ?? 'Pending'; ?></td>
                        <td>
                            <button class="approve" data-id="<?php echo $row['id']; ?>" data-action="approve">✔️</button>
                            <button class="deny" data-id="<?php echo $row['id']; ?>" data-action="deny">❌</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.querySelectorAll('.approve, .deny').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('employerId').value = this.dataset.id;
                document.getElementById('actionType').value = this.dataset.action;
                document.getElementById('actionForm').submit();
            });
        });

        document.getElementById('searchBar').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('#employersTable tr').forEach(row => {
                const companyName = row.children[1].textContent.toLowerCase();
                row.style.display = companyName.includes(filter) ? '' : 'none';
            });
        });

        document.getElementById('statusFilter').addEventListener('change', function() {
            const filter = this.value;
            document.querySelectorAll('#employersTable tr').forEach(row => {
                row.style.display = (filter === 'all' || row.dataset.status === filter) ? '' : 'none';
            });
        });

        // Fetch unread notifications count periodically
        function fetchUnreadNotificationsCount() {
            fetch('fetch_notifications_count.php')
                .then(response => response.text())
                .then(count => {
                    document.querySelector('.notification-count').textContent = count;
                });
        }

        // Poll every 10 seconds (adjust as needed)
        setInterval(fetchUnreadNotificationsCount, 10000);

        // Handle click event on the notification button
        document.querySelector('.notification-button').addEventListener('click', function() {
            fetch('fetch_notifications.php')
                .then(response => response.json())
                .then(notifications => {
                    alert('Unread Notifications:\n' + notifications.join('\n'));
                });
        });

    </script>
</body>
</html>
