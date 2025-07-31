<?php
session_start();
include 'db.php';

if (!isset($_POST['id']) || !isset($_POST['quantity'])) {
    header("Location: cart.php");
    exit();
}
// Determine the current user's identifier (logged‑in or guest)
if (!isset($_SESSION['username']) && !isset($_SESSION['guest_id'])) {
    $_SESSION['guest_id'] = 'guest_' . bin2hex(random_bytes(5));
}
$user_identifier = $_SESSION['username'] ?? $_SESSION['guest_id'];

$id = (int)$_POST['id'];
$quantity = max(1, (int)$_POST['quantity']);

// Update quantity only for items belonging to the current user
$stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_identifier = ?");
$stmt->bind_param("iis", $quantity, $id, $user_identifier);
$stmt->execute();

header("Location: cart.php");
exit();
