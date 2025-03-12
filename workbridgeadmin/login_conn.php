<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employer_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    
    // Verify the password
    if (password_verify($password, $hashedPassword)) {
        echo "Login successful!";
        // Redirect to the dashboard or any other page
    } else {
        echo "Invalid username or password.";
    }
    $stmt->close();
}

$conn->close();
?>
