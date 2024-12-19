<?php
$conn = new mysqli("localhost", "root", "", "lalamons_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$province = isset($_GET['province']) ? $_GET['province'] : '';

$result = $conn->query("SELECT DISTINCT municipality AS name FROM address WHERE province = '$province' AND municipality != ''");

$municipalities = [];
while ($row = $result->fetch_assoc()) {
    $municipalities[] = ['name' => $row['name']];
}

header('Content-Type: application/json');
echo json_encode($municipalities);
?>
