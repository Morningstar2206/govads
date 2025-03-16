<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "virtual_marketplace";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

if (empty($_POST['full-name']) || empty($_POST['email']) || empty($_POST['password'])) {
    die(json_encode(["status" => "error", "message" => "All fields are required."]));
}

$name = trim($_POST['full-name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(["status" => "error", "message" => "Invalid email format."]));
}

if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
    die(json_encode(["status" => "error", "message" => "Full name can only contain letters and spaces."]));
}

$sql = "SELECT id FROM businesses WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die(json_encode(["status" => "error", "message" => "Email already exists. Please use a different email."]));
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO businesses (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Admin signup successful! Redirecting to login..."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>