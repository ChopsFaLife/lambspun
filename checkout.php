<?php
// Checkout page to review cart contents and create a Stripe Checkout session
// This file replaces the previous remove‑item placeholder. It displays
// the current user/guest cart and offers a button to pay via Stripe.

session_start();
require 'db.php';

// Determine user identifier (logged in user or guest). For guests
// we generate a unique identifier stored in the session so their
// cart items persist across page loads.
if (!isset($_SESSION['username']) && !isset($_SESSION['guest_id'])) {
    $_SESSION['guest_id'] = 'guest_' . bin2hex(random_bytes(5));
}
$user_identifier = $_SESSION['username'] ?? $_SESSION['guest_id'];

// Fetch cart items for the current user
$stmt = $conn->prepare("SELECT id, product_name, price, quantity FROM cart WHERE user_identifier = ?");
$stmt->bind_param("s", $user_identifier);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0.0;
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
}
$stmt->close();

// Define your Stripe publishable key for the client‑side checkout
// It’s recommended to store this in an environment variable or config file.
$stripePublishableKey = 'pk_live_XXXXXXXXXXXXXXXXXXXXXXXX';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout – Lambspun Florals</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h1>Review Your Order</h1>

    <?php if (empty($items)): ?>
        <p>Your cart is empty. <a href="shop.html">Continue shopping</a>.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <h3>Total: $<?= number_format($total, 2) ?></h3>

        <!-- Checkout button -->
        <button id="checkout-button">Pay with Card</button>

        <script>
        // Initialize Stripe with your publishable key
        var stripe = Stripe("<?= $stripePublishableKey ?>");

        document.getElementById('checkout-button').addEventListener('click', function () {
            // Call your server to create a Checkout session
            fetch('create-checkout-session.php', {
                method: 'POST'
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (session) {
                if (session.error) {
                    alert('Error: ' + session.error);
                    return;
                }
                return stripe.redirectToCheckout({ sessionId: session.id });
            })
            .then(function (result) {
                if (result && result.error) {
                    alert(result.error.message);
                }
            })
            .catch(function (error) {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
            });
        });
        </script>
    <?php endif; ?>

    <p><a href="cart.php">← Back to Cart</a></p>
</body>
</html>