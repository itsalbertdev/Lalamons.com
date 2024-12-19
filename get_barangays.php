<?php
$conn = new mysqli("localhost", "root", "", "lalamons_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$municipality = isset($_GET['municipality']) ? $_GET['municipality'] : '';

$result = $conn->query("SELECT DISTINCT barangay AS name FROM address WHERE municipality = '$municipality' AND barangay != ''");

$barangays = [];
while ($row = $result->fetch_assoc()) {
    $barangays[] = ['name' => $row['name']];
}

header('Content-Type: application/json');
echo json_encode($barangays);
?>
