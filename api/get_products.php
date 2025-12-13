<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
include "config.php";

$sql = "
SELECT 
    p.id,
    p.title,
    p.price,
    p.mrp,
    p.short_description,
    p.image_url,  -- 💡 ADDED: Select the image_url column
    c.name AS category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.is_active = 1
ORDER BY p.created_at DESC
";

$result = $conn->query($sql);

// 🔴 CHECK QUERY ERROR
if (!$result) {
    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);
    exit;
}

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode([
    "success" => true,
    "products" => $products
]);

// Remember to update your index.html JavaScript to use p.image_url for the <img> src!
// <img src="${p.image_url}" alt="${p.title}">