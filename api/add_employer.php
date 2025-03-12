<?php
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employer_db"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$company_name = $data['company_name'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash the password for security

$sql = "INSERT INTO employers (company_name, email, password) VALUES ('$company_name', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['message' => 'Employer added successfully']);
} else {
    echo json_encode(['error' => 'Error: ' . $sql . '<br>' . $conn->error]);
}

$conn->close();
?>
