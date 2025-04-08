<?php
// API endpoint voor het verwijderen van gebruikers door admin

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Afhandelen van OPTIONS request (pre-flight voor CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

// Controleren of het een POST request is
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Alleen POST requests worden geaccepteerd']);
    exit();
}

// Database configuratie laden
require_once '../../config/database.php';

// Input data ophalen
$input = json_decode(file_get_contents('php://input'), true);

// Valideer input
if (!isset($input['userId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Gebruiker ID is vereist']);
    exit();
}

$userIdToDelete = intval($input['userId']);

// Authenticatie header ophalen
$headers = getallheaders();
$adminUserId = null;
$isAdmin = false;

// Valideer of er een Authorization header is met een user ID
if (isset($headers['Authorization']) && preg_match('/User\s+(\d+)/', $headers['Authorization'], $matches)) {
    $adminUserId = $matches[1];
    
    // Database verbinding maken
    $conn = getDbConnection();
    
    // Controleer of gebruiker admin is
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $adminUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $isAdmin = ($user['role'] === 'admin');
    }
    
    $stmt->close();
}

// Als gebruiker geen admin is, stuur toegang geweigerd bericht
if (!$isAdmin) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Toegang geweigerd. Alleen administrators mogen gebruikers verwijderen.'
    ]);
    if (isset($conn)) $conn->close();
    exit();
}

// Zorg ervoor dat de admin zichzelf niet kan verwijderen
if (intval($adminUserId) === $userIdToDelete) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'U kunt uw eigen admin account niet verwijderen.'
    ]);
    $conn->close();
    exit();
}

// Database verbinding gebruiken of opnieuw maken als nodig
if (!isset($conn)) {
    $conn = getDbConnection();
}

// Controleer of gebruiker bestaat en geen admin is
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $userIdToDelete);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Gebruiker niet gevonden.'
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

$user = $result->fetch_assoc();
if ($user['role'] === 'admin') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Admin gebruikers kunnen niet worden verwijderd.'
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

$stmt->close();

// Gebruiker verwijderen
$deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$deleteStmt->bind_param("i", $userIdToDelete);

if ($deleteStmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Gebruiker succesvol verwijderd.',
        'deletedUserId' => $userIdToDelete
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Er is een fout opgetreden bij het verwijderen van de gebruiker: ' . $deleteStmt->error
    ]);
}

// Verbinding sluiten
$deleteStmt->close();
$conn->close();
?> 