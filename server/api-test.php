<?php
// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Basic server info
$serverInfo = [
    'status' => 'success',
    'message' => 'PHP API is working correctly',
    'time' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown'
];

// Add database check if available
try {
    require_once 'config/database.php';
    
    // Test database connection
    $conn = getDbConnection();
    $serverInfo['database'] = [
        'status' => 'connected',
        'connection_info' => 'Successfully connected to database'
    ];
    
    // Check if users table exists and has records
    $query = "SELECT COUNT(*) as count FROM users";
    $result = $conn->query($query);
    
    if ($result) {
        $row = $result->fetch_assoc();
        $serverInfo['database']['users_count'] = (int)$row['count'];
    } else {
        $serverInfo['database']['users_table'] = 'Error: ' . $conn->error;
    }
    
    $conn->close();
} catch (Exception $e) {
    $serverInfo['database'] = [
        'status' => 'error',
        'error' => $e->getMessage()
    ];
}

// Return response
echo json_encode($serverInfo, JSON_PRETTY_PRINT);
?> 