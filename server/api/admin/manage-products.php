<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../../config/database.php';

// Start session
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    echo json_encode([
        "success" => false,
        "error" => "Geen toegang tot deze pagina"
    ]);
    exit();
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Create database connection
$conn = getDbConnection();

// Process request based on method
switch ($method) {
    case 'GET':
        // Get all products or a specific product
        $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($product_id > 0) {
            // Get specific product
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
            
            echo json_encode([
                "success" => true,
                "product" => $product
            ]);
            
            $stmt->close();
        } else {
            // Get all products with pagination
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            $sql = "SELECT * FROM products LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            // Get total count for pagination
            $sql_count = "SELECT COUNT(*) as total FROM products";
            $result_count = $conn->query($sql_count);
            $total = $result_count->fetch_assoc()['total'];
            $total_pages = ceil($total / $limit);
            
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
        }
        break;
        
    case 'POST':
        // Create new product
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Check if all required fields are present
        if (!isset($data["name"]) || !isset($data["price"])) {
            echo json_encode([
                "success" => false,
                "error" => "Naam en prijs zijn vereist"
            ]);
            exit();
        }
        
        $name = $data["name"];
        $description = isset($data["description"]) ? $data["description"] : "";
        $price = (float)$data["price"];
        $category = isset($data["category"]) ? $data["category"] : "";
        $image = isset($data["image"]) ? $data["image"] : "";
        $stock = isset($data["stock"]) ? (int)$data["stock"] : 0;
        
        // Insert product
        $sql = "INSERT INTO products (name, description, price, category, image, stock) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssi", $name, $description, $price, $category, $image, $stock);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Product succesvol toegevoegd",
                "product_id" => $conn->insert_id
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Kon product niet toevoegen: " . $conn->error
            ]);
        }
        
        $stmt->close();
        break;
        
    case 'PUT':
        // Update product
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Check if product ID is provided
        if (!isset($data["id"])) {
            echo json_encode([
                "success" => false,
                "error" => "Product ID is vereist"
            ]);
            exit();
        }
        
        $product_id = (int)$data["id"];
        
        // Check if product exists
        $check_sql = "SELECT id FROM products WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            echo json_encode([
                "success" => false,
                "error" => "Product niet gevonden"
            ]);
            $check_stmt->close();
            $conn->close();
            exit();
        }
        
        $check_stmt->close();
        
        // Build update query
        $updates = [];
        $params = [];
        $param_types = "";
        
        if (isset($data["name"])) {
            $updates[] = "name = ?";
            $params[] = $data["name"];
            $param_types .= "s";
        }
        
        if (isset($data["description"])) {
            $updates[] = "description = ?";
            $params[] = $data["description"];
            $param_types .= "s";
        }
        
        if (isset($data["price"])) {
            $updates[] = "price = ?";
            $params[] = (float)$data["price"];
            $param_types .= "d";
        }
        
        if (isset($data["category"])) {
            $updates[] = "category = ?";
            $params[] = $data["category"];
            $param_types .= "s";
        }
        
        if (isset($data["image"])) {
            $updates[] = "image = ?";
            $params[] = $data["image"];
            $param_types .= "s";
        }
        
        if (isset($data["stock"])) {
            $updates[] = "stock = ?";
            $params[] = (int)$data["stock"];
            $param_types .= "i";
        }
        
        if (empty($updates)) {
            echo json_encode([
                "success" => false,
                "error" => "Geen velden om bij te werken"
            ]);
            exit();
        }
        
        // Add product_id to params
        $params[] = $product_id;
        $param_types .= "i";
        
        // Execute update
        $sql = "UPDATE products SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($param_types, ...$params);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Product succesvol bijgewerkt"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Kon product niet bijwerken: " . $conn->error
            ]);
        }
        
        $stmt->close();
        break;
        
    case 'DELETE':
        // Delete product
        $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($product_id <= 0) {
            echo json_encode([
                "success" => false,
                "error" => "Product ID is vereist"
            ]);
            exit();
        }
        
        // Check if product exists
        $check_sql = "SELECT id FROM products WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            echo json_encode([
                "success" => false,
                "error" => "Product niet gevonden"
            ]);
            $check_stmt->close();
            $conn->close();
            exit();
        }
        
        $check_stmt->close();
        
        // Delete product
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Product succesvol verwijderd"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Kon product niet verwijderen: " . $conn->error
            ]);
        }
        
        $stmt->close();
        break;
        
    default:
        echo json_encode([
            "success" => false,
            "error" => "Methode niet ondersteund"
        ]);
        break;
}

$conn->close();
?> 