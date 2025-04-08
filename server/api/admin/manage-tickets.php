<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, PUT, POST");
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
        // Get all tickets or a specific ticket
        $ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($ticket_id > 0) {
            // Get specific ticket
            $sql = "SELECT * FROM contact_messages WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $ticket_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                echo json_encode([
                    "success" => false,
                    "error" => "Ticket niet gevonden"
                ]);
                $stmt->close();
                $conn->close();
                exit();
            }
            
            $ticket = $result->fetch_assoc();
            
            echo json_encode([
                "success" => true,
                "ticket" => $ticket
            ]);
            
            $stmt->close();
        } else {
            // Get all tickets with pagination and filters
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            $status = isset($_GET['status']) ? $_GET['status'] : '';
            $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at';
            $sort_order = isset($_GET['sort_order']) && strtolower($_GET['sort_order']) === 'asc' ? 'ASC' : 'DESC';
            
            // Validate sort_by to prevent SQL injection
            $valid_sort_fields = ['id', 'name', 'email', 'subject', 'status', 'created_at', 'updated_at'];
            if (!in_array($sort_by, $valid_sort_fields)) {
                $sort_by = 'created_at';
            }
            
            $query = "SELECT * FROM contact_messages WHERE 1=1";
            $count_query = "SELECT COUNT(*) as total FROM contact_messages WHERE 1=1";
            
            $params = [];
            $param_types = "";
            
            // Add status filter if provided
            if (!empty($status)) {
                $query .= " AND status = ?";
                $count_query .= " AND status = ?";
                $params[] = $status;
                $param_types .= "s";
            }
            
            // Add sorting and pagination
            $query .= " ORDER BY $sort_by $sort_order LIMIT ? OFFSET ?";
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
            
            $tickets = [];
            while ($row = $result->fetch_assoc()) {
                $tickets[] = $row;
            }
            
            // Get total count for pagination
            $stmt_count = $conn->prepare($count_query);
            if (!empty($status) && $param_types !== "ii") {
                $count_param_types = substr($param_types, 0, -2);
                $count_params = array_slice($params, 0, -2);
                $stmt_count->bind_param($count_param_types, ...$count_params);
            }
            $stmt_count->execute();
            $count_result = $stmt_count->get_result();
            $total = $count_result->fetch_assoc()['total'];
            
            $total_pages = ceil($total / $limit);
            
            // Get counts by status for the filters
            $status_counts = [
                'all' => $total,
                'open' => 0,
                'in_progress' => 0,
                'closed' => 0
            ];
            
            $sql_status_counts = "SELECT status, COUNT(*) as count FROM contact_messages GROUP BY status";
            $result_status_counts = $conn->query($sql_status_counts);
            while ($row = $result_status_counts->fetch_assoc()) {
                $status_counts[$row['status']] = (int)$row['count'];
            }
            
            echo json_encode([
                "success" => true,
                "tickets" => $tickets,
                "status_counts" => $status_counts,
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
        // Update ticket status
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Check if ticket ID and status are provided
        if (!isset($data["id"]) || !isset($data["status"])) {
            echo json_encode([
                "success" => false,
                "error" => "Ticket ID en status zijn vereist"
            ]);
            exit();
        }
        
        $ticket_id = (int)$data["id"];
        $status = $data["status"];
        
        // Validate status
        $valid_statuses = ['open', 'in_progress', 'closed'];
        if (!in_array($status, $valid_statuses)) {
            echo json_encode([
                "success" => false,
                "error" => "Ongeldige status. Geldige waarden zijn: " . implode(", ", $valid_statuses)
            ]);
            exit();
        }
        
        // Check if ticket exists
        $check_sql = "SELECT id FROM contact_messages WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $ticket_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            echo json_encode([
                "success" => false,
                "error" => "Ticket niet gevonden"
            ]);
            $check_stmt->close();
            $conn->close();
            exit();
        }
        
        $check_stmt->close();
        
        // Update ticket status
        $sql = "UPDATE contact_messages SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $ticket_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Ticket status succesvol bijgewerkt"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Kon ticket status niet bijwerken: " . $conn->error
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