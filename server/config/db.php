<!--
    ============================================
    Author: Apothecare Team
    Description: Database configuration for Apothecare
    ============================================
-->
<?php
$host = "sql7.freesqldatabase.com";
$user = "sql7771094";  
$password = "nJ6RY1iX8k"; 
$database = "sql7771094";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]));
}

return $conn;
?> 