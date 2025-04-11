<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
    
    <!-- controlleerd welke rol is gekozen en bepaald of er een code moet ingevuld worden-->
    <script>
        function toggleAdminCodeField() {
            var role = document.getElementById("role").value;
            var adminCodeField = document.getElementById("adminCodeField");

            if (role === "admin") {
                adminCodeField.style.display = "block";
            } else {
                adminCodeField.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <h2>Register</h2>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="email">email:</label>
        <input type="email" id="email" name="email" required>

        <label for="role">Role:</label>
        <select id="role" name="role" onchange="toggleAdminCodeField()">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <!-- Dit is het veld voor de admin-code dat alleen wordt weergegeven als "admin" is geselecteerd -->
        <div id="adminCodeField" style="display:none;">
            <label for="admin_code">Admin Code:</label>
            <input type="password" id="admin_code" name="admin_code">
        </div>

        <button type="submit">Register</button>
    </form>
</body>
</html>

<?php
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Admin code die moet worden ingevoerd
    $correct_admin_code = "123456"; // vervang dit door je eigen admin code

    // Controleer of de rol "admin" is en valideer de admin-code
    if ($role === "admin") {
        $admin_code = $_POST['admin_code'] ?? ''; // Zorg ervoor dat er geen fout optreedt als dit veld leeg is
        if ($admin_code !== $correct_admin_code) {
            echo "Onjuiste admin code!";
            exit();
        }
    }

    // Als de admin-code correct is (of als de rol niet "admin" is), voer de registratie uit
    $sql = "INSERT INTO users (username, password, role, email) VALUES (?, ?, ?)";
    $mysqli = require 'database.php';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    
    // Na succesvolle registratie, doorverwijzen naar de loginpagina
    header("Location: login.php");
    exit();
}
