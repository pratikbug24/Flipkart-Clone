<?php
// get_cart_items.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle CORS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include "config.php"; // Include your database connection

// --- 1. Get User ID ---
// In a real application, you would pass a session token or user ID via POST/GET
// For simplicity here, we assume the user_id is passed as a GET parameter.
// NOTE: You must update the frontend to pass the user ID correctly.
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0; 

if ($user_id <= 0) {
    http_response_code(400); 
    echo json_encode(["success" => false, "message" => "User ID is missing."]);
    exit;
}

// 2. SQL Query to fetch cart items
$sql = "
    SELECT 
        c.id as cart_item_id,
        c.product_id,
        c.quantity,
        p.title,
        p.price,
        p.image_url,
        p.mrp,
        (c.quantity * p.price) AS total_price_item
    FROM carts c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database query failed: " . $conn->error]);
    exit;
}

$cart_items = [];
$cart_subtotal = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $cart_subtotal += $row['total_price_item'];
}

// 3. Return the results
echo json_encode([
    "success" => true,
    "items" => $cart_items,
    "subtotal" => number_format($cart_subtotal, 2, '.', '')
]);

$conn->close();
?>