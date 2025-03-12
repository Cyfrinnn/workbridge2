<?php
// Get the posted data.
$postData = file_get_contents("php://input");
$request = json_decode($postData, true);

$email = $request['email'];
$password = $request['password'];
$loginType = $request['loginType'];

// Validate user credentials
$mysqli = new mysqli("localhost", "root", "", "employer_db");

if ($mysqli->connect_errno) {
    echo json_encode(["error" => "Failed to connect to MySQL: " . $mysqli->connect_error]);
    exit();
}

if ($loginType == 'applicant') {
    $stmt = $mysqli->prepare("SELECT password FROM applicants WHERE email = ?");
} elseif ($loginType == 'employer') {
    $stmt = $mysqli->prepare("SELECT password FROM employers WHERE email = ?");
} else {
    echo json_encode(["error" => "Invalid login type"]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

$response = [];

if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    if (password_verify($password, $hashedPassword)) {
        $response["message"] = "Login successful";
    } else {
        $response["error"] = "Invalid email or password";
    }
} else {
    $response["error"] = "Invalid email or password";
}

echo json_encode($response);

$stmt->close();
$mysqli->close();
?>
