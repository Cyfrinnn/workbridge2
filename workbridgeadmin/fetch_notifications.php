<?php
include('config.php'); // Include your database connection

// Fetch unread notifications
$result = $conn->query("SELECT message FROM notifications WHERE is_read = FALSE");

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row['message'];
}

// Mark notifications as read
$conn->query("UPDATE notifications SET is_read = TRUE WHERE is_read = FALSE");

// Return notifications as JSON
echo json_encode($notifications);
?>
