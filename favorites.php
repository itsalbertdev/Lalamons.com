<?php
session_start();
require 'db_connection.php'; // Include your database connection

// Simulated logged-in user (replace this with actual session data)
$user_id = $_SESSION['user_id'] ?? 1;

// Step 1: Fetch favorite product IDs for the user
$query = "SELECT product_id FROM favorites WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$favorites = [];
while ($row = $result->fetch_assoc()) {
    $favorites[] = $row['product_id'];
}

// Step 2: Fetch product details for the favorite products
$favorite_products = [];
if (!empty($favorites)) {
    $placeholders = implode(',', array_fill(0, count($favorites), '?')); // Prepare placeholders for IN clause
    $types = str_repeat('i', count($favorites)); // Data types (all integers)
    $query = "SELECT product_id, product_name, price, description, image_url FROM products WHERE product_id IN ($placeholders)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$favorites);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $favorite_products[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Favorite Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #b22222;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        header nav a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }
        .container {
            padding: 20px;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 10px;
            overflow: hidden;
            display: flex;
            background-color: #fff;
        }
        .product-card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
        .product-details {
            padding: 15px;
        }
        .product-details h3 {
            margin: 0 0 10px;
            font-size: 20px;
            color: #333;
        }
        .product-details p {
            margin: 5px 0;
            color: #555;
        }
        .remove-btn {
            background-color: #b22222;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<header>
    <h1>LALAMONS</h1>
    <nav>
        <a href="main.php">Home</a>
        <a href="cart.php">Cart</a>
        <a href="favorites.php">Favorites</a>
        <a href="profile.php">Profile</a>
    </nav>
</header>

<div class="container">
    <h2>Your Favorite Products</h2>

    <?php if (empty($favorite_products)): ?>
        <p>No favorite products added yet!</p>
    <?php else: ?>
        <?php foreach ($favorite_products as $product): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                <div class="product-details">
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <button class="remove-btn" onclick="removeFavorite(<?php echo $product['product_id']; ?>)">
                        Remove from Favorites
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
// AJAX function to remove product from favorites
function removeFavorite(productId) {
    fetch('update_favorites.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product removed from favorites.');
            location.reload(); // Reload the page
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to communicate with the server.');
    });
}
</script>
</body>
</html>
