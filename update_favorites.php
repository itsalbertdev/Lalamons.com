<?php
session_start();
header('Content-Type: application/json');
require 'db_connection.php';

$user_id = $_SESSION['user_id'] ?? 1; // Replace with actual user session logic

// Debugging: Check if input is received
$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'] ?? null;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Product ID missing.']);
    exit;
}

// Debugging: Check DB connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection error.']);
    exit;
}

// Check if the product is already in favorites
$checkQuery = "SELECT * FROM favorites WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($checkQuery);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database query error: ' . $conn->error]);
    exit;
}
$stmt->bind_param('ii', $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Remove from favorites
    $deleteQuery = "DELETE FROM favorites WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Delete query failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Removed from favorites.']);
} else {
    // Add to favorites
    $insertQuery = "INSERT INTO favorites (user_id, product_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Insert query failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Added to favorites.']);
}
?>
