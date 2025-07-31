<?php
session_start();
require_once 'db.php';
// then use $conn as the database connection

if (!isset($_SESSION['username'])) {
    if (!isset($_SESSION['guest_id'])) {
        $_SESSION['guest_id'] = 'guest_' . bin2hex(random_bytes(5));
    }
    $user = $_SESSION['guest_id'];
} else {
    $user = $_SESSION['username'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    $product_name = $_POST['product_name'];
    $quantity = (int)$_POST['quantity'];
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.00;

    // Insert item into cart table. Use user_identifier instead of username so
    // guests and logged‑in users share the same column.
    $stmt = $conn->prepare("INSERT INTO cart (user_identifier, product_name, quantity, price) VALUES (?, ?, ?, ?)");
    // Bind parameters in the order: user (string), product name (string), price (double), quantity (int)
    // Using ssdi means: s=string (user), s=string (product name), d=double (price), i=integer (quantity)
    $stmt->bind_param("ssdi", $user, $product_name, $price, $quantity);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: cart.php");
    exit();
}
?>
