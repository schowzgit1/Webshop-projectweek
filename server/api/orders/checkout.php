<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../../config/database.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "error" => "Je moet ingelogd zijn om een bestelling te plaatsen"
    ]);
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    echo json_encode([
        "success" => false,
        "error" => "Je winkelwagen is leeg"
    ]);
    exit();
}

// Get user ID from session
$user_id = $_SESSION["user_id"];

// Create database connection
$conn = getDbConnection();

// Calculate total price
$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Begin transaction
$conn->begin_transaction();

try {
    // Create order
    $sql_order = "INSERT INTO orders (user_id, total_price) VALUES (?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("id", $user_id, $total_price);
    $stmt_order->execute();
    
    $order_id = $conn->insert_id;
    
    // Insert order items
    $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_items = $conn->prepare($sql_items);
    
    // Update product stock
    $sql_update_stock = "UPDATE products SET stock = stock - ? WHERE id = ?";
    $stmt_stock = $conn->prepare($sql_update_stock);
    
    foreach ($_SESSION['cart'] as $item) {
        // Add item to order_items
        $item_price = $item['price'];
        $stmt_items->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item_price);
        $stmt_items->execute();
        
        // Update product stock
        $stmt_stock->bind_param("ii", $item['quantity'], $item['product_id']);
        $stmt_stock->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    // Clear cart after successful order
    $_SESSION['cart'] = [];
    
    // Return success response
    echo json_encode([
        "success" => true,
        "message" => "Bestelling succesvol geplaatst",
        "order_id" => $order_id
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    echo json_encode([
        "success" => false,
        "error" => "Er is een fout opgetreden bij het plaatsen van je bestelling: " . $e->getMessage()
    ]);
}

// Close statements and connection
$stmt_order->close();
$stmt_items->close();
$stmt_stock->close();
$conn->close();
?> 