<!--
    ============================================
    Author: Apothecare Team
    Description: Contact form API endpoint for Apothecare
    ============================================
-->
<?php
header("Access-Control-Allow-Origin: *"); // Sta CORS toe als Nuxt en API zich op verschillende servers bevinden
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../config/db.php';
$conn = require_once '../config/db.php';

// Haal JSON-gegevens op uit het verzoek
$data = json_decode(file_get_contents("php://input"), true);

// Controleren of alle gegevens beschikbaar zijn
if (!isset($data["name"]) || !isset($data["email"]) || !isset($data["message"])) {
    echo json_encode(["success" => false, "error" => "Vul alle velden in"]);
    exit();
}

$name = $conn->real_escape_string($data["name"]);
$email = $conn->real_escape_string($data["email"]);
$subject = isset($data["subject"]) ? $conn->real_escape_string($data["subject"]) : "Geen onderwerp";
$message = $conn->real_escape_string($data["message"]);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "error" => "Ongeldig e-mailadres"]);
    exit();
}

// SQL-query
$sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Uw bericht is verzonden!"]);
} else {
    echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
}

$conn->close();
?>

