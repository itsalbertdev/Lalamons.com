<?php
// Fetch cart items from the session
session_start();
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

echo json_encode($cart);
?>
