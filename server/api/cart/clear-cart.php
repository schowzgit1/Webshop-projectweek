<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Start session
session_start();

// Clear the cart
$_SESSION['cart'] = [];

// Return success response
echo json_encode([
    "success" => true,
    "message" => "Winkelwagen is geleegd",
    "cart" => []
]);
?> 