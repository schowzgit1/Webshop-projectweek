<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../config/db.php';
$conn = require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["email"]) || !isset($data["password"])) {
    echo json_encode(["success" => false, "error" => "Vul alle velden in"]);
    exit();
}

$email = $conn->real_escape_string($data["email"]);
$password = $data["password"];

$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        echo json_encode(["success" => true, "message" => "Succesvol ingelogd"]);
    } else {
        echo json_encode(["success" => false, "error" => "Verkeerd wachtwoord of email"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Verkeerd wachtwoord of email"]);
}

$conn->close();
?> 