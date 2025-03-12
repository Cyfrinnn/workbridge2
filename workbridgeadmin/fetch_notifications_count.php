<?php
include('config.php'); // Include your database connection

$unread_notifications_count = $conn->query("SELECT COUNT(*) AS count FROM notifications WHERE is_read = FALSE")->fetch_assoc()['count'];

echo $unread_notifications_count;
?>
