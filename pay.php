<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, #f44336, #d32f2f);
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; 
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px; 
            width: 100%; 
            text-align: center;
        }
        .message {
            margin-top: 20px;
            font-size: 18px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
    <script>
        console.log("Payment processing script loaded.");
    </script>
</head>
<body>

<div class="container">
<?php
include('db_connection.php');
session_start(); // Start the session to access cart data

// Retrieve product ID from URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($product_id === 0) {
    echo "<div class='message error'>No product ID provided. Please access this page with a valid product ID.</div>";
    exit();
}

$query = "SELECT * FROM products WHERE product_id = $product_id"; // Ensure the correct column name is used
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<div class='message error'>Product not found.</div>";
    exit();
}

// Set the amount directly from the product's price in centavos
$amount = (int)($product['price'] * 100); // Convert price to centavos

// Automatically process payment logic
echo "<script>console.log('Processing payment for product ID: " . $product_id . "');</script>";

// Set up the API endpoint and authorization
$url = 'https://api.paymongo.com/v1/links';
$apiKey = 'sk_test_wrn3Mw35ohbgeD232TJkRgjK';
$data = [
    'data' => [
        'attributes' => [
            'amount' => $amount,  // Pass the amount in centavos (integer)
            'description' => 'Payment for ' . $product['product_name'], // Use product name for description
            'currency' => 'PHP',
            'redirect' => [
                'success' => 'https://yourwebsite.com/success',
                'failed' => 'https://yourwebsite.com/failed'
            ]
        ]
    ]
];

// Set up cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($apiKey . ':')  // Use base64-encoded secret key
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute cURL request
$response = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "<script>console.log('HTTP Status: " . $httpStatus . "');</script>";

// Check for errors
if ($httpStatus != 200) {
    echo "<div class='message error'>Failed to create payment link. HTTP Status: $httpStatus</div>";
    echo "<div class='message'>Response: " . htmlspecialchars($response) . "</div>";
} else {
    // Parse the response
    $responseData = json_decode($response, true);
    if (isset($responseData['data']['attributes']['checkout_url'])) {
        echo "<div class='product-details' style='margin-top: 20px;'>";
        echo "<h3 style='text-align: center; margin-bottom: 15px;'>Product Details</h3>";
        echo "<div class='product-label' style='display: flex; align-items: center; justify-content: center;'>";
        echo "<img src='" . $product['image_url'] . "' alt='" . $product['product_name'] . "' style='width: 200px; height: auto; margin-right: 20px;'>";
        echo "<div style='text-align: left;'>";
        echo "<p><strong>Product Name:</strong> " . $product['product_name'] . "</p>";
        echo "<p><strong>Amount:</strong> Php " . number_format($product['price'], 2) . "</p>"; // Display the product price
        echo "<p><strong>Description:</strong> " . $product['description'] . "</p>";
        echo "</div></div>";
        echo "<div class='message success' style='text-align: center; margin-top: 20px;'>Pay your order here: <a href='" . $responseData['data']['attributes']['checkout_url'] . "' target='_blank'>Click here to pay</a></div>";
    } else {
        echo "<div class='message error'>Failed to create payment link. Response: " . htmlspecialchars($response) . "</div>";
    }
}

curl_close($ch);
?>
</div>

</body>
</html>
