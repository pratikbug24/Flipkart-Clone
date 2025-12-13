<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
include "config.php"; // Your DB connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle quantity update
if (isset($_POST['update_cart'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    if ($quantity > 0) {
        $stmt = $conn->prepare("UPDATE carts SET quantity=? WHERE id=? AND user_id=?");
        $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
        $stmt->execute();
    }
}

// Handle remove item
if (isset($_POST['remove_cart'])) {
    $cart_id = intval($_POST['cart_id']);
    $stmt = $conn->prepare("DELETE FROM carts WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
}

// Fetch cart items
$sql = "
    SELECT c.id AS cart_id, p.id AS product_id, p.title, p.price, p.mrp, c.quantity
    FROM carts c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

// Pass data to the frontend
include "cart.html";
