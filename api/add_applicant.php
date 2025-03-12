<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
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

// Decode JSON data from POST request
$data = json_decode(file_get_contents('php://input'), true);

// Collect POST data
$full_name = $data['full_name'];
$email = $data['email'];
$password = $data['password'];

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert data into applicant table
$sql = "INSERT INTO applicants (full_name, email, password) VALUES ('$full_name', '$email', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(array("status" => "success", "message" => "Applicant registered successfully."));
} else {
    echo json_encode(array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error));
}

$conn->close();
?>
