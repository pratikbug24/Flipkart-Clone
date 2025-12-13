<?php
// Debug mode for local dev
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    echo json_encode(["success" => true, "message" => "CORS OK"]);
    exit;
}

try {
    // DB connection
    $conn = new mysqli("localhost", "root", "", "flipkart_clone");
    if ($conn->connect_error) {
        throw new Exception("DB connection failed: " . $conn->connect_error);
    }

    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) throw new Exception("Invalid JSON input");

    $full_name = trim($data['name'] ?? '');
    $email     = trim($data['email'] ?? '');
    $mobile    = trim($data['mobile'] ?? '');
    $password  = trim($data['password'] ?? '');

    // Validation
    if (!$full_name || !$email || !$mobile || !$password) {
        throw new Exception("All fields are required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    if (!preg_match("/^[0-9]{10}$/", $mobile)) {
        throw new Exception("Invalid 10-digit mobile number");
    }

    if (strlen($password) < 6) {
        throw new Exception("Password must be at least 6 characters");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $mobile, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registration successful"]);
    } else {
        if ($conn->errno == 1062) {
            echo json_encode(["success" => false, "message" => "Email or Mobile already exists"]);
        } else {
            throw new Exception("Database insert failed: " . $conn->error);
        }
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
