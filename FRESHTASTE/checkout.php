<?php
session_start();
header('Content-Type: text/plain'); // importante ito!

$conn = new mysqli("localhost", "root", "", "myfreshtaste_db");

if ($conn->connect_error) {
    echo "DB Error: " . $conn->connect_error;
    exit;
}

if ($_POST) {
    $items = $_POST['items'] ?? '';
    $total = floatval($_POST['total'] ?? 0);
    $name = $_POST['name'] ?? 'Guest';
    $cid = intval($_POST['cid'] ?? 0);

    // DAGDAG NA: status = 'pending' (or 'confirmed', 'preparing', etc.)
    $status = 'pending';

    $sql = "INSERT INTO orders (customer_id, customer_name, items, total, order_date, status) 
            VALUES (?, ?, ?, ?, NOW(), ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    // "issds" → i = int, s = string, d = double, s = string (status)
    $stmt->bind_param("issds", $cid, $name, $items, $total, $status);

    if ($stmt->execute()) {
        echo "success"; // EXACTO lang ito ang ipapadala
    } else {
        echo "Execute failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>