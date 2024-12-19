<?php
// get_postal_code.php

if (isset($_GET['barangay'])) {
    $barangay = $_GET['barangay'];

    $conn = new mysqli("localhost", "root", "", "lalamons_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT postal_code FROM address WHERE barangay = ?");
    $stmt->bind_param("s", $barangay);
    $stmt->execute();
    $stmt->bind_result($postal_code);
    $stmt->fetch();

    echo json_encode(['postal_code' => $postal_code]);

    $stmt->close();
    $conn->close();
}
?>