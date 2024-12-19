<?php
session_start();

// Check if the cart is set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = $_SESSION['cart'];
$totalPrice = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }
        header {
            background-color: #b22222;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .cart-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
        }
        .total {
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
    <h1>Your Cart</h1>
</header>
<main>
    <?php if (empty($cartItems)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <?php foreach ($cartItems as $item): ?>
            <div class="cart-item">
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <div>
                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                    <p>Price: PHP <?php echo htmlspecialchars($item['price']); ?></p>
                    <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                </div>
            </div>
            <?php $totalPrice += $item['price'] * $item['quantity']; ?>
        <?php endforeach; ?>
        <div class="total">Total Price: PHP <?php echo $totalPrice; ?></div>
    <?php endif; ?>
</main>
</body>
</html>
