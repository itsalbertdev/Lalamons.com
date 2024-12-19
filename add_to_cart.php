<?php
session_start(); // Start the session to access session variables

// Database configuration
$host = 'localhost'; // Change to localhost
$username = 'root';
$password = '';
$dbname = 'lalamons_db';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$productId = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1; // Default to 1 if not provided

error_log("Session variables: " . print_r($_SESSION, true)); // Log session variables for debugging
error_log("Received product_id: $productId, quantity: $quantity");
// Ensure quantity is an integer
$quantity = intval($quantity) > 0 ? intval($quantity) : 1;

error_log("Session user_id: " . $_SESSION['user_id']); // Log the user ID for debugging

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['message' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['user_id'];

// Check if product is already in the cart
$sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['message' => 'Product already in cart.']);
    $stmt->close();
    $conn->close();
    exit();
}

// Add product to user's cart
$sql = "INSERT INTO cart (user_id, product_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $productId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product added to cart successfully.']);
} else {
    // Log the error for debugging
    error_log("Failed to add product to cart: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to add product to cart.'. $stmt->error]);
}

$stmt->close();
$conn->close();
?>
