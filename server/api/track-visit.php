<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../config/database.php';

// Start session
session_start();

// Get JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

// Check if page is specified
if (!isset($data["page"])) {
    echo json_encode([
        "success" => false,
        "error" => "Pagina moet worden opgegeven"
    ]);
    exit();
}

$page_visited = $data["page"];
$ip_address = $_SERVER['REMOTE_ADDR'];

// Create database connection
$conn = getDbConnection();

// Check if user is logged in
$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

// Insert visit record
if ($user_id) {
    $sql = "INSERT INTO visits (user_id, ip_address, page_visited) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $ip_address, $page_visited);
} else {
    $sql = "INSERT INTO visits (ip_address, page_visited) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $ip_address, $page_visited);
}

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Bezoek geregistreerd"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Kon bezoek niet registreren: " . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?> 