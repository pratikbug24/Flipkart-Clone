<?php
// add_to_cart.php
// Set required headers for CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Crucial: allow POST and OPTIONS
header("Access-Control-Allow-Headers: Content-Type");

// 🔑 CORS PREFLIGHT FIX: Handle the OPTIONS request
// The browser sends an OPTIONS request first to check permissions.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Must return OK status
    exit(); // Exit gracefully, preventing PHP logic/errors from interfering
}

// ----------------------------------------------------
// Start of Main Logic (Only runs if request is POST)
// ----------------------------------------------------

// Include database config
include "config.php"; 

// Get the raw POST data
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// Check for required data
// Note: These checks ensure your front-end JS is working correctly
if (empty($data['product_id']) || empty($data['user_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Missing product_id or user_id"]);
    exit;
}

$product_id = $data['product_id'];
$user_id = $data['user_id'];
$quantity = 1; // Default quantity to add

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// 1. Check if the item is already in the cart for this user
$check_sql = "SELECT id, quantity FROM carts WHERE user_id = ? AND product_id = ?";
$stmt_check = $conn->prepare($check_sql);
$stmt_check->bind_param("ii", $user_id, $product_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // 2. If it exists, UPDATE the quantity
    $row = $result_check->fetch_assoc();
    $new_quantity = $row['quantity'] + 1;
    $cart_item_id = $row['id'];
    
    $update_sql = "UPDATE carts SET quantity = ?, updated_at = NOW() WHERE id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("ii", $new_quantity, $cart_item_id);
    $stmt_update->execute();
    
    if ($stmt_update->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Product quantity updated to " . $new_quantity . " in cart."]);
    } else {
        echo json_encode(["success" => true, "message" => "Product already in cart and quantity was not changed."]);
    }

} else {
    // 3. If it doesn't exist, INSERT new item
    $insert_sql = "INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("iii", $user_id, $product_id, $quantity);
    $stmt_insert->execute();

    if ($stmt_insert->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Product added to cart successfully."]);
    } else {
        // Output detailed error if insert fails
        http_response_code(500); 
        echo json_encode(["success" => false, "message" => "Failed to add product to cart: " . $conn->error]);
    }
}

$conn->close();
?>