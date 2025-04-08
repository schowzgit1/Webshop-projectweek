<?php
// Login API endpoint

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Alleen POST requests worden geaccepteerd']);
    exit();
}

// Database configuratie
$host = 'sql7.freesqldatabase.com';
$user = 'sql7771094';
$password = 'nJ6RY1iX8k';
$database = 'sql7771094';

try {
    // Maak database verbinding
    $conn = new mysqli($host, $user, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception('Database verbinding mislukt');
    }

    // Haal login data op
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['username']) || !isset($data['password'])) {
        throw new Exception('Gebruikersnaam en wachtwoord zijn verplicht');
    }

    // Controleer gebruiker
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $data['username']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Gebruikersnaam bestaat niet');
    }

    $user = $result->fetch_assoc();

    if (password_verify($data['password'], $user['password'])) {
        unset($user['password']);
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        throw new Exception('Ongeldig wachtwoord');
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 