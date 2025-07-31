<?php
session_start();
include 'db.php';

// Determine user identity
if (!isset($_SESSION['username']) && !isset($_SESSION['guest_id'])) {
    $_SESSION['guest_id'] = 'guest_' . bin2hex(random_bytes(5));
}
$user = $_SESSION['username'] ?? $_SESSION['guest_id'];

$stmt = $conn->prepare("SELECT id, product_name, price, quantity FROM cart WHERE user_identifier = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Your Shopping Cart</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Price (Each)</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): 
                $subtotal = $row['price'] * $row['quantity'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td>$<?= number_format($row['price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>$<?= number_format($subtotal, 2) ?></td>
                <td>
                    <form method="POST" action="remove_from_cart.php">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit">Remove</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <h3>Total: $<?= number_format($total, 2) ?></h3>
        <a href="checkout.php"><button>Proceed to Checkout</button></a>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
