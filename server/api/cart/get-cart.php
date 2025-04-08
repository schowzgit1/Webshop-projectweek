<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Start session
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Calculate cart totals
$total_price = 0;
$total_items = 0;

foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['quantity'];
    $total_items += $item['quantity'];
}

// Return cart data
echo json_encode([
    "success" => true,
    "cart" => $_SESSION['cart'],
    "summary" => [
        "total_price" => $total_price,
        "total_items" => $total_items
    ]
]);
?> 