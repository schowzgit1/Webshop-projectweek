<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["name"]) || !isset($data["email"]) || !isset($data["message"])) {
    echo json_encode(["success" => false, "error" => "Vul alle velden in"]);
    exit();
}

$conn = getDbConnection();

$name = $conn->real_escape_string($data["name"]);
$email = $conn->real_escape_string($data["email"]);
$subject = isset($data["subject"]) ? $conn->real_escape_string($data["subject"]) : "Geen onderwerp";
$message = $conn->real_escape_string($data["message"]);
$status = "open";

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "error" => "Ongeldig e-mailadres"]);
    exit();
}


$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Prepare failed: " . $conn->error]);
    exit();
}

$stmt->bind_param("ssss", $name, $email, $subject, $message);

if ($stmt->execute()) {
    // Логирование посещения
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $page_visited = 'contact';

    session_start();
    $user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

    if ($user_id) {
        $log_visit = "INSERT INTO visits (user_id, ip_address, page_visited) VALUES (?, ?, ?)";
        $stmt_visit = $conn->prepare($log_visit);
        $stmt_visit->bind_param("iss", $user_id, $ip_address, $page_visited);
        $stmt_visit->execute();
        $stmt_visit->close();
    } else {
        $log_visit = "INSERT INTO visits (ip_address, page_visited) VALUES (?, ?)";
        $stmt_visit = $conn->prepare($log_visit);
        $stmt_visit->bind_param("ss", $ip_address, $page_visited);
        $stmt_visit->execute();
        $stmt_visit->close();
    }

    echo json_encode(["success" => true, "message" => "Uw bericht is verzonden! We nemen zo snel mogelijk contact met u op."]);
} else {
    echo json_encode(["success" => false, "error" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

