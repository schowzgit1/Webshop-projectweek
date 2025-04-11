<script setup>
import { ref } from "vue";
 
const name = ref("");
const email = ref("");
const subject = ref("Algemene vraag");
const message = ref("");
const responseMessage = ref("");
const responseClass = ref("");
const loading = ref(false);
 
const submitForm = async () => {
  loading.value = true;
 
  const { data, error } = await useFetch("http://localhost/webshop-projectweek/server/api/contact.php", {
    method: "POST",
    body: {
      name: name.value,
      email: email.value,
      subject: subject.value,
      message: message.value
    },
  });
 
  if (error.value) {
    responseMessage.value = "Kan geen verbinding maken met de server.";
    responseClass.value = "error";
  } else if (data.value?.success) {
    responseMessage.value = "Uw bericht is verzonden!";
    responseClass.value = "success";
    name.value = "";
    email.value = "";
    message.value = "";
  } else {
    responseMessage.value = "Er is een fout opgetreden: " + data.value?.error;
    responseClass.value = "error";
  }
 
  loading.value = false;
};
</script>
<?php
// CORS headers for cross-domain requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Verify that this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "error" => "Only POST requests are allowed"]);
    exit();
}

// Database connection settings
$host = "localhost";
$user = "root";
$password = "";
$database = "apothecare";

// Create database connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Database connection failed: " . $conn->connect_error
    ]);
    exit();
}

// Get JSON data from the request
$json = file_get_contents("php://input");
$data = json_decode($json, true);

// Debug - log the received data
error_log("Received contact form data: " . $json);

// Validate input data
if (!$data || !isset($data["name"]) || !isset($data["email"]) || !isset($data["message"])) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "success" => false, 
        "error" => "Missing required fields. Please provide name, email, and message."
    ]);
    exit();
}

// Sanitize input data
$name = $conn->real_escape_string(trim($data["name"]));
$email = $conn->real_escape_string(trim($data["email"]));
$subject = isset($data["subject"]) ? $conn->real_escape_string(trim($data["subject"])) : "Geen onderwerp";
$message = $conn->real_escape_string(trim($data["message"]));

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "error" => "Ongeldig e-mailadres formaat"
    ]);
    exit();
}

// Check if the contact_messages table exists, if not create it
$checkTableSql = "SHOW TABLES LIKE 'contact_messages'";
$tableExists = $conn->query($checkTableSql)->num_rows > 0;

if (!$tableExists) {
    $createTableSql = "CREATE TABLE contact_messages (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($createTableSql)) {
        http_response_code(500);
        echo json_encode([
            "success" => false, 
            "error" => "Could not create database table: " . $conn->error
        ]);
        exit();
    }
}

// Insert the contact message into the database
$sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "SQL preparation failed: " . $conn->error
    ]);
    exit();
}

$stmt->bind_param("ssss", $name, $email, $subject, $message);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true, 
        "message" => "Uw bericht is succesvol verzonden!"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Database error: " . $stmt->error
    ]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>