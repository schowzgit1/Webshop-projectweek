<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['message'])) {
        throw new Exception('Message is required');
    }

    // Debug log
    error_log("Received message: " . $data['message']);

    // Mistral AI API configuration
    $apiUrl = 'http://localhost:7860/v1/chat/completions';

    // Prepare the request to Mistral AI
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'messages' => [
            [
                'role' => 'user',
                'content' => $data['message']
            ]
        ]
    ]));

    // Debug log
    error_log("Sending request to Mistral AI...");

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Debug log
    error_log("Response code: " . $httpCode);
    error_log("Response: " . $response);

    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }
    
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception('Mistral AI API error: ' . $response);
    }

    $aiResponse = json_decode($response, true);
    
    if (!isset($aiResponse['choices'][0]['message']['content'])) {
        throw new Exception('Invalid response format from Mistral AI');
    }

    echo json_encode([
        'success' => true,
        'response' => $aiResponse['choices'][0]['message']['content']
    ]);

} catch (Exception $e) {
    error_log("Error in chat.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 