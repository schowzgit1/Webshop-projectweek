<?php

// Database instellingen
$host = "sql7.freesqldatabase.com";
$user = "sql7771094"; 
$password = "nJ6RY1iX8k";
$database = "sql7771094";

// Maak verbinding met de database

$mysqli = new mysqli($host, $user, $password, $database);

try {
    if ($mysqli->connect_errno) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
} catch (Exception $e) {
    die("Fout opgetreden: " . $e->getMessage());
}

// Als alles goed gaat:
return $mysqli;

?>
