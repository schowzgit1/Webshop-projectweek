<?php
// API endpoint voor het ophalen van gebruikers

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Afhandelen van OPTIONS request (pre-flight voor CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

// Database configuratie laden
require_once '../../config/database.php';

// Authenticatie header ophalen
$headers = getallheaders();
$userId = null;
$isAdmin = false;

// Valideer of er een Authorization header is met een user ID
if (isset($headers['Authorization']) && preg_match('/User\s+(\d+)/', $headers['Authorization'], $matches)) {
    $userId = $matches[1];
    
    // Database verbinding maken
    $conn = getDbConnection();
    
    // Controleer of gebruiker admin is
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
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
        'message' => 'Toegang geweigerd. Alleen administrators hebben toegang tot deze data.'
    ]);
    if (isset($conn)) $conn->close();
    exit();
}

// Database verbinding gebruiken of opnieuw maken als nodig
if (!isset($conn)) {
    $conn = getDbConnection();
}

// Alle gebruikers ophalen
$users = [];
$query = "SELECT id, username, email, role, created_at FROM users ORDER BY id ASC";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Gebruikers terugsturen
echo json_encode([
    'success' => true,
    'users' => $users
]);

// Verbinding sluiten
$conn->close();
?> 