<?php
$conn = new mysqli("localhost", "root", "", "lalamons_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$region = isset($_GET['region']) ? $_GET['region'] : '';

$result = $conn->query("SELECT DISTINCT province AS name FROM address WHERE region = '$region' AND province != ''");

$provinces = [];
while ($row = $result->fetch_assoc()) {
    $provinces[] = ['name' => $row['name']];
}

header('Content-Type: application/json');
echo json_encode($provinces);
?>
