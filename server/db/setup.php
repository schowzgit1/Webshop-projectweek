<?php
// Dit script maakt de benodigde database tabellen aan

// Database configuratie
require_once '../config/database.php';

echo "Database setup starten...\n";

// Functie om een query uit te voeren
function execute_query($conn, $sql, $message) {
    echo "Uitvoeren: $message... ";
    
    if ($conn->query($sql) === TRUE) {
        echo "Voltooid!\n";
        return true;
    } else {
        echo "FOUT: " . $conn->error . "\n";
        return false;
    }
}

// Verbinding maken
$conn = getDbConnection();

// Users tabel
$users_sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME
)";

execute_query($conn, $users_sql, "Aanmaken users tabel");

// Products tabel
$products_sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME
)";

execute_query($conn, $products_sql, "Aanmaken products tabel");

// Orders tabel
$orders_sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    payment_method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

execute_query($conn, $orders_sql, "Aanmaken orders tabel");

// Order_items tabel
$order_items_sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

execute_query($conn, $order_items_sql, "Aanmaken order_items tabel");

// Contact_messages (tickets) tabel
$contact_messages_sql = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('open', 'in_progress', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME
)";

execute_query($conn, $contact_messages_sql, "Aanmaken contact_messages tabel");

// Visits tabel
$visits_sql = "CREATE TABLE IF NOT EXISTS visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    page_visited VARCHAR(100),
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

execute_query($conn, $visits_sql, "Aanmaken visits tabel");

// Controleer of er al sample data is
$check_products = "SELECT COUNT(*) as count FROM products";
$result = $conn->query($check_products);

if ($result && $result->fetch_assoc()['count'] == 0) {
    // Voeg enkele voorbeeld producten toe
    $sample_products = "INSERT INTO products (name, description, price, stock, category) VALUES
    ('Paracetamol 500mg', 'Pijnstillende medicatie voor hoofdpijn en koorts', 4.99, 100, 'pijnstillers'),
    ('Ibuprofen 400mg', 'Ontstekingsremmende pijnstiller', 5.99, 80, 'pijnstillers'),
    ('Vitamines B-Complex', 'Essentiële B-vitamines voor dagelijks gebruik', 12.50, 45, 'vitamines'),
    ('Neusspray', 'Verlichting bij verstopte neus', 3.99, 60, 'verkoudheidsmiddelen'),
    ('Multivitamine', 'Complete dagelijkse vitamines', 14.99, 30, 'vitamines')";

    execute_query($conn, $sample_products, "Toevoegen voorbeeld producten");
}

// Controleer of er al een admin gebruiker bestaat
$check_admin = "SELECT COUNT(*) as count FROM users WHERE role = 'admin'";
$result = $conn->query($check_admin);

if ($result && $result->fetch_assoc()['count'] == 0) {
    echo "Geen admin gebruiker gevonden, je kunt er één aanmaken met het create-admin.php script.\n";
}

$conn->close();

echo "\nDatabase setup voltooid! Je kunt nu inloggen met de admin gebruiker of nieuwe gebruikers registreren.\n";
?> 