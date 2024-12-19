<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'lalamons_db';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
            product_id AS id, 
            product_name AS name, 
            image_url AS img, 
            price, 
            category_id 
        FROM products 
        WHERE status = 'available'";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['price'] = (float)$row['price'];
        $products[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($products);
} else {
    echo json_encode([]);
}

$conn->close();
?>
