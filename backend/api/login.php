<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Output buffering to prevent stray output
ob_start();

// CORS Headers
header("Access-Control-Allow-Origin: http://localhost:4200"); // Specify frontend origin
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    http_response_code(200);
    ob_end_flush();
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "news_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    ob_end_flush();
    exit();
}

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['action']) && $data['action'] === 'login' && isset($data['username']) && isset($data['password'])) {
    $username = $data['username'];
    $password = $data['password'];

    // Use prepared statements for security
    $sql_login = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $sql_login->bind_param("s", $username);
    $sql_login->execute();
    $result = $sql_login->get_result();

    if ($result === false) {
        echo json_encode(["success" => false, "message" => "Query failed: " . $conn->error]);
        ob_end_flush();
        exit();
    }

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $sql_token = $conn->prepare("INSERT INTO tokens (user_id, token, expires_at, type) VALUES (?, ?, ?, 'auth')");
            $sql_token->bind_param("iss", $user['id'], $token, $expires_at);

            if (!$sql_token->execute()) {
                echo json_encode(["success" => false, "message" => "Error inserting token: " . $conn->error]);
                ob_end_flush();
                exit();
            }

            echo json_encode(["success" => true, "token" => $token, "user" => $user]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid credentials."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
ob_end_flush();
?>
