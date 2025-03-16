<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "virtual_marketplace";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

$email = $_POST['email'];

$sql = "SELECT id FROM admins WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $token = bin2hex(random_bytes(32));

    $sql = "UPDATE admins SET reset_token = ?, token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();

    $reset_link = "http://yourwebsite.com/admin-reset-password.html?token=$token";
    $subject = "Admin Password Reset Request";
    $message = "Click the link below to reset your password:\n\n$reset_link";
    $headers = "From: no-reply@yourwebsite.com";

    if (mail($email, $subject, $message, $headers)) {
        echo json_encode(["status" => "success", "message" => "A password reset link has been sent to your email."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to send email. Please try again."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Email not found."]);
}

$stmt->close();
$conn->close();
?>