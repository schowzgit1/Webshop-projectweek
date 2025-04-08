<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, PUT");
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
        // Get all orders or a specific order
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($order_id > 0) {
            // Get specific order with its items
            $sql = "
                SELECT o.*, u.username 
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                echo json_encode([
                    "success" => false,
                    "error" => "Bestelling niet gevonden"
                ]);
                $stmt->close();
                $conn->close();
                exit();
            }
            
            $order = $result->fetch_assoc();
            
            // Get order items
            $sql_items = "
                SELECT oi.*, p.name, p.image 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ";
            $stmt_items = $conn->prepare($sql_items);
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();
            
            $items = [];
            while ($row = $result_items->fetch_assoc()) {
                $items[] = $row;
            }
            
            $order['items'] = $items;
            
            echo json_encode([
                "success" => true,
                "order" => $order
            ]);
            
            $stmt->close();
            $stmt_items->close();
        } else {
            // Get all orders with pagination
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            $status = isset($_GET['status']) ? $_GET['status'] : '';
            
            $query = "
                SELECT o.*, u.username 
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE 1=1
            ";
            $count_query = "
                SELECT COUNT(*) as total 
                FROM orders o
                WHERE 1=1
            ";
            
            $params = [];
            $param_types = "";
            
            // Add status filter if provided
            if (!empty($status)) {
                $query .= " AND o.status = ?";
                $count_query .= " AND o.status = ?";
                $params[] = $status;
                $param_types .= "s";
            }
            
            // Add pagination
            $query .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            $param_types .= "ii";
            
            // Execute query
            $stmt = $conn->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($param_types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $orders = [];
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
            
            // Get total count for pagination
            $stmt_count = $conn->prepare($count_query);
            if (!empty($status)) {
                $stmt_count->bind_param("s", $status);
            }
            $stmt_count->execute();
            $count_result = $stmt_count->get_result();
            $total = $count_result->fetch_assoc()['total'];
            
            $total_pages = ceil($total / $limit);
            
            echo json_encode([
                "success" => true,
                "orders" => $orders,
                "pagination" => [
                    "currentPage" => $page,
                    "totalPages" => $total_pages,
                    "totalItems" => $total,
                    "itemsPerPage" => $limit
                ]
            ]);
            
            $stmt->close();
            $stmt_count->close();
        }
        break;
        
    case 'PUT':
        // Update order status
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Check if order ID and status are provided
        if (!isset($data["id"]) || !isset($data["status"])) {
            echo json_encode([
                "success" => false,
                "error" => "Bestelling ID en status zijn vereist"
            ]);
            exit();
        }
        
        $order_id = (int)$data["id"];
        $status = $data["status"];
        
        // Validate status
        $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
        if (!in_array($status, $valid_statuses)) {
            echo json_encode([
                "success" => false,
                "error" => "Ongeldige status. Geldige waarden zijn: " . implode(", ", $valid_statuses)
            ]);
            exit();
        }
        
        // Check if order exists
        $check_sql = "SELECT id FROM orders WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $order_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            echo json_encode([
                "success" => false,
                "error" => "Bestelling niet gevonden"
            ]);
            $check_stmt->close();
            $conn->close();
            exit();
        }
        
        $check_stmt->close();
        
        // Update order status
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $order_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Bestelling status succesvol bijgewerkt"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Kon bestelling status niet bijwerken: " . $conn->error
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