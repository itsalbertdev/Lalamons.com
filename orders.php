<?php
// orders.php
// Dummy data for demonstration
$orders = [
    ["order_id" => 101, "item" => "Laptop", "status" => "Delivered", "date" => "2024-12-10"],
    ["order_id" => 102, "item" => "Headphones", "status" => "Processing", "date" => "2024-12-11"],
    ["order_id" => 103, "item" => "Smartphone", "status" => "Shipped", "date" => "2024-12-09"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #d32f2f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background: #d32f2f;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Orders</h1>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Item</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order["order_id"]); ?></td>
                <td><?= htmlspecialchars($order["item"]); ?></td>
                <td><?= htmlspecialchars($order["status"]); ?></td>
                <td><?= htmlspecialchars($order["date"]); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
