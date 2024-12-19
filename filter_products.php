<?php
include 'db_connection.php'; // Adjust this to your database connection file

$search = $_GET['search'] ?? ''; // Get search query
$category_id = $_GET['category_id'] ?? 'all'; // Get category filter

// Sanitize inputs
$search = $conn->real_escape_string($search);
$category_id = $conn->real_escape_string($category_id);

// Build SQL query
$sql = "SELECT p.product_id, p.product_name, p.price, p.description, p.image_url, c.category_name
        FROM products p
        JOIN category c ON p.category_id = c.category_id
        WHERE p.status = 'available'";

if ($category_id !== 'all') {
    $sql .= " AND p.category_id = '$category_id'";
}
if (!empty($search)) {
    $sql .= " AND p.product_name LIKE '%$search%'";
}

$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
?>
