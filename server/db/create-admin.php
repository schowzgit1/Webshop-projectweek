<?php
// Dit script maakt een admin-gebruiker aan in de database

// Database configuratie
require_once '../config/database.php';

// Admin gegevens
$admin_username = 'admin';
$admin_email = 'admin@webshop.nl';
$admin_password = 'admin123';
$admin_role = 'admin';

// Verbinding maken
$conn = getDbConnection();

// Controleer of admin al bestaat
$check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ss", $admin_username, $admin_email);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "Admin gebruiker bestaat al met ID: " . $user['id'] . "\n";
    echo "Als je het wachtwoord wilt resetten, gebruik dan dit SQL commando:\n";
    echo "UPDATE users SET password = '" . password_hash($admin_password, PASSWORD_DEFAULT) . "' WHERE id = " . $user['id'] . ";\n";
} else {
    // Hash wachtwoord
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    // Voeg admin gebruiker toe
    $insert_sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ssss", $admin_username, $admin_email, $hashed_password, $admin_role);
    
    if ($insert_stmt->execute()) {
        $admin_id = $conn->insert_id;
        echo "Admin gebruiker succesvol aangemaakt met ID: " . $admin_id . "\n";
        echo "Gebruik deze gegevens om in te loggen:\n";
        echo "Gebruikersnaam: " . $admin_username . "\n";
        echo "Wachtwoord: " . $admin_password . "\n";
    } else {
        echo "Fout bij het aanmaken van admin gebruiker: " . $insert_stmt->error . "\n";
    }
    
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();

echo "\nJe kunt nu inloggen op de admin dashboard via /login\n";
?> 