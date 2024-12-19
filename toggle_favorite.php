<?php
// Database configuration
$host = 'laazth';
$username = 'laazth';
$password = 'coleeyves';
$dbname = 'lalamons_db';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$input = json_decode(file_get_contents("php://input"), true);
$productId = $input['productId'];

// Assume a session or some mechanism to track the user
$userId = $_SESSION['user_id'];

// Toggle favorite status
$sql = "SELECT * FROM favorites WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Product is already a favorite, remove it
    $sql = "DELETE FROM favorites WHERE user_id = ? AND product_id = ?";
} else {
    // Product is not a favorite, add it
    $sql = "INSERT INTO favorites (user_id, product_id) VALUES (?, ?)";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $productId);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Favorite status toggled successfully.']);
} else {
    echo json_encode(['message' => 'Failed to toggle favorite status.']);
}

$stmt->close();
$conn->close();
?>
