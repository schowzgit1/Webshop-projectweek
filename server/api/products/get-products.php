<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../../config/database.php';

// Get query parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate offset for pagination
$offset = ($page - 1) * $limit;

// Create database connection
$conn = getDbConnection();

// Build query based on parameters
$query = "SELECT * FROM products WHERE 1=1";
$count_query = "SELECT COUNT(*) as total FROM products WHERE 1=1";
$params = [];
$param_types = "";

// Add category filter if provided
if (!empty($category) && $category !== 'alle') {
    $query .= " AND category = ?";
    $count_query .= " AND category = ?";
    $params[] = $category;
    $param_types .= "s";
}

// Add search filter if provided
if (!empty($search)) {
    $search_term = "%$search%";
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $count_query .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = $search_term;
    $params[] = $search_term;
    $param_types .= "ss";
}

// Add pagination
$query .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$param_types .= "ii";

// Prepare and execute query for products
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Get total count for pagination
$stmt_count = $conn->prepare($count_query);
if (!empty($param_types) && $param_types !== "ii") {
    // Remove the "ii" for limit and offset
    $count_param_types = substr($param_types, 0, -2);
    $count_params = array_slice($params, 0, -2);
    $stmt_count->bind_param($count_param_types, ...$count_params);
}
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$total = $count_result->fetch_assoc()['total'];

$total_pages = ceil($total / $limit);

// Return response
echo json_encode([
    "success" => true,
    "products" => $products,
    "pagination" => [
        "currentPage" => $page,
        "totalPages" => $total_pages,
        "totalItems" => $total,
        "itemsPerPage" => $limit
    ]
]);

$stmt->close();
$stmt_count->close();
$conn->close();
?> 