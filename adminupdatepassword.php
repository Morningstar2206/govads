<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "virtual_marketplace";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

$new_password = $_POST['new-password'];
$confirm_password = $_POST['confirm-password'];
$token = $_GET['token'];

if ($new_password !== $confirm_password) {
    die(json_encode(["status" => "error", "message" => "Passwords do not match."]));
}

$sql = "SELECT id FROM admins WHERE reset_token = ? AND token_expiry > NOW()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE admins SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed_password, $token);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Password updated successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid or expired token."]);
}

$stmt->close();
$conn->close();
?>