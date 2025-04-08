<?php
require_once '../config/database.php';

function executeQuery($conn, $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Success: " . $sql . "\n";
    } else {
        echo "Error: " . $sql . "\n" . $conn->error . "\n";
    }
}

// Get database connection
$conn = getDbConnection();

// Create users table
$sql = "DROP TABLE IF EXISTS users;";
executeQuery($conn, $sql);

$sql = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME
);";
executeQuery($conn, $sql);

// Create admin user
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT INTO users (username, email, password, role, updated_at) VALUES 
('admin', 'admin@webshop.nl', '$adminPassword', 'admin', NOW());";
executeQuery($conn, $sql);

echo "\nDatabase setup completed!\n";
echo "You can now log in with:\n";
echo "Username: admin\n";
echo "Password: admin123\n";

$conn->close();
?> 