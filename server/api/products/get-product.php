<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../../config/database.php';

// Get product ID from URL parameter
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "Product ID is vereist"
    ]);
    exit();
}

// Create database connection
$conn = getDbConnection();

// Get product details
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

// Get related products (same category)
$sql_related = "SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4";
$stmt_related = $conn->prepare($sql_related);
$stmt_related->bind_param("si", $product["category"], $product_id);
$stmt_related->execute();
$result_related = $stmt_related->get_result();

$related_products = [];
while ($row = $result_related->fetch_assoc()) {
    $related_products[] = $row;
}

// Log the product view in visits table if user is logged in
session_start();
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $page_visited = "product-" . $product_id;
    
    $log_visit = "INSERT INTO visits (user_id, ip_address, page_visited) VALUES (?, ?, ?)";
    $stmt_visit = $conn->prepare($log_visit);
    $stmt_visit->bind_param("iss", $user_id, $ip_address, $page_visited);
    $stmt_visit->execute();
    $stmt_visit->close();
}

// Return response with product and related products
echo json_encode([
    "success" => true,
    "product" => $product,
    "relatedProducts" => $related_products
]);

$stmt->close();
$stmt_related->close();
$conn->close();
?> 