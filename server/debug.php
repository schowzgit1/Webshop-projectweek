<?php
// Basic information about PHP
echo "<h1>PHP Debug Info</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current Script: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";

// Database connection test
echo "<h2>Database Connection Test</h2>";
try {
    require_once 'config/database.php';
    $conn = getDbConnection();
    echo "<p style='color:green;'>Database connection successful!</p>";
    
    // Test query for users table
    $query = "SELECT COUNT(*) as count FROM users";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    echo "<p>Number of users in database: " . $row['count'] . "</p>";
    
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color:red;'>Database connection failed: " . $e->getMessage() . "</p>";
}

// Session test
echo "<h2>Session Test</h2>";
session_start();
echo "<p>Session ID: " . session_id() . "</p>";
$_SESSION['test'] = 'This is a test value: ' . date('Y-m-d H:i:s');
echo "<p>Session test value set: " . $_SESSION['test'] . "</p>";

// Include/require test
echo "<h2>Include Path Test</h2>";
echo "<p>include_path: " . get_include_path() . "</p>";

// File permissions test
echo "<h2>File Permissions Test</h2>";
$testFile = "debug_test.txt";
$fp = @fopen($testFile, 'w');
if ($fp) {
    fwrite($fp, "Test at " . date('Y-m-d H:i:s'));
    fclose($fp);
    echo "<p style='color:green;'>Successfully wrote to test file</p>";
    echo "<p>File content: " . file_get_contents($testFile) . "</p>";
    unlink($testFile);
} else {
    echo "<p style='color:red;'>Could not write to test file. Check directory permissions.</p>";
}

// User agent info
echo "<h2>Client Info</h2>";
echo "<p>User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "</p>";
echo "<p>Client IP: " . $_SERVER['REMOTE_ADDR'] . "</p>";
?> 