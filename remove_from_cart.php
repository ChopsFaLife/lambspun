<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    // Get user identity (logged in or guest)
    $user_identifier = $_SESSION['username'] ?? $_SESSION['guest_id'] ?? '';

    // Secure deletion only if the item belongs to the current user
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_identifier = ?");
    $stmt->bind_param("is", $id, $user_identifier);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: cart.php");
exit();
