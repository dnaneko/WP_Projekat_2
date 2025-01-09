<?php
// CORS handling
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json"); // Ensure JSON response type

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "news_portal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON request if sent with Content-Type: application/json
    $postData = json_decode(file_get_contents('php://input'), true);

    // Validate action
    if (!isset($postData['action']) || $postData['action'] !== 'register') {
        echo json_encode(["success" => false, "message" => "Invalid action."]);
        exit();
    }

    // Extract and sanitize data
    $name = $postData['name'] ?? '';
    $lastName = $postData['lastName'] ?? '';
    $username = $postData['username'] ?? '';
    $password = $postData['password'] ?? '';
    $email = $postData['email'] ?? '';

    // Validate required fields
    if (empty($name) || empty($lastName) || empty($username) || empty($password) || empty($email)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $sql_register = "
        INSERT INTO users (name, lastName, username, password, email, status, role) 
        VALUES (?, ?, ?, ?, ?, 'Pending', 'Standard')
    ";

    $stmt = $conn->prepare($sql_register);
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Failed to prepare the statement: " . $conn->error]);
        exit();
    }

    // Bind parameters
    $stmt->bind_param("sssss", $name, $lastName, $username, $hashedPassword, $email);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registration successful."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
    exit();
}

echo json_encode(["success" => false, "message" => "Invalid request method."]);
exit();
?>
