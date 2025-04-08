<?php
header('Content-Type: application/json');

// Test if PHP is executing properly
$response = [
    'status' => 'success',
    'message' => 'PHP is working correctly',
    'time' => date('Y-m-d H:i:s'),
    'phpVersion' => phpversion()
];

echo json_encode($response);
?> 