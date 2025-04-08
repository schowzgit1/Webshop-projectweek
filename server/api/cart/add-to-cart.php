<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../../config/database.php';

// Start session
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

// Check if all required fields are present
if (!isset($data["product_id"]) || !isset($data["quantity"])) {
    echo json_encode([
        "success" => false, 
        "error" => "Product ID en hoeveelheid zijn vereist"
    ]);
    exit();
}

$product_id = (int)$data["product_id"];
$quantity = (int)$data["quantity"];

// Validate quantity
if ($quantity <= 0) {
    echo json_encode([
        "success" => false, 
        "error" => "Hoeveelheid moet groter zijn dan 0"
    ]);
    exit();
}

// Create database connection
$conn = getDbConnection();

// Check if product exists and get its details
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "error" => "Product niet gevonden"
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

$product = $result->fetch_assoc();

// Check if we have enough stock
if ($product['stock'] < $quantity) {
    echo json_encode([
        "success" => false,
        "error" => "Niet genoeg voorraad. Beschikbaar: " . $product['stock']
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

// Add to cart
$cart_item = [
    "product_id" => $product_id,
    "name" => $product["name"],
    "price" => (float)$product["price"],
    "quantity" => $quantity,
    "image" => $product["image"]
];

// Check if product already exists in cart
$found = false;
foreach ($_SESSION['cart'] as $key => $item) {
    if ($item['product_id'] === $product_id) {
        $_SESSION['cart'][$key]['quantity'] += $quantity;
        $found = true;
        break;
    }
}

// If not found, add as new item
if (!$found) {
    $_SESSION['cart'][] = $cart_item;
}

// Return updated cart
echo json_encode([
    "success" => true,
    "message" => "Product toegevoegd aan winkelwagen",
    "cart" => $_SESSION['cart']
]);

$stmt->close();
$conn->close();
?> 