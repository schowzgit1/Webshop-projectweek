<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../config/db.php';
$conn = require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["email"]) || !isset($data["password"]) || !isset($data["name"])) {
    echo json_encode(["success" => false, "error" => "Vul alle velden in"]);
    exit();
}

$email = $conn->real_escape_string($data["email"]);
$name = $conn->real_escape_string($data["name"]);
$password = password_hash($data["password"], PASSWORD_DEFAULT);

// Check if email already exists
$check_sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "Email bestaat al"]);
    exit();
}

$sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Account succesvol aangemaakt"]);
} else {
    echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
}

$conn->close();
?> 