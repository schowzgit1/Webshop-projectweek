<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../../config/database.php';

// Start session
session_start();

// Check if cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    echo json_encode([
        "success" => false,
        "error" => "Winkelwagen is leeg",
        "cart" => []
    ]);
    exit();
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

// Create database connection
$conn = getDbConnection();

// If quantity is 0, remove item from cart
if ($quantity <= 0) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] === $product_id) {
            unset($_SESSION['cart'][$key]);
            // Reindex array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
    
    echo json_encode([
        "success" => true,
        "message" => "Product verwijderd uit winkelwagen",
        "cart" => $_SESSION['cart']
    ]);
    $conn->close();
    exit();
}

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

// Update item quantity
$found = false;
foreach ($_SESSION['cart'] as $key => $item) {
    if ($item['product_id'] === $product_id) {
        $_SESSION['cart'][$key]['quantity'] = $quantity;
        $found = true;
        break;
    }
}

// If not found, return error
if (!$found) {
    echo json_encode([
        "success" => false,
        "error" => "Product niet gevonden in winkelwagen"
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

// Return updated cart
echo json_encode([
    "success" => true,
    "message" => "Winkelwagen bijgewerkt",
    "cart" => $_SESSION['cart']
]);

$stmt->close();
$conn->close();
?> 