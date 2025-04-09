<!--
    ============================================
    Author: Apothecare Team
    Description: Check login status API endpoint for Apothecare
    ============================================
-->
<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

session_start();

echo json_encode([
    "loggedIn" => isset($_SESSION['user_id']),
    "user" => isset($_SESSION['user_id']) ? [
        "id" => $_SESSION['user_id'],
        "email" => $_SESSION['email']
    ] : null
]);
?> 