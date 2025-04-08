<?php
// Database configuratie

// Database instellingen
$host = "sql7.freesqldatabase.com";
$user = "sql7771094"; 
$password = "nJ6RY1iX8k";
$database = "sql7771094";

/**
 * Maakt een database verbinding en geeft deze terug
 * 
 * @return mysqli Database verbinding
 */
function getDbConnection() {
    global $host, $user, $password, $database;
    
    // Verbinding maken
    $conn = new mysqli($host, $user, $password, $database);
    
    // Controleer verbinding
    if ($conn->connect_error) {
        die("Database verbinding mislukt: " . $conn->connect_error);
    }
    
    // UTF-8 instellen
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

/**
 * Sluit een database verbinding
 * 
 * @param mysqli $conn Database verbinding om te sluiten
 */
function closeDbConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?> 