<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit('Not logged in.');
}

$username = $_SESSION['username'];
$method = $_POST['method'] ?? 'Unknown';
$amount = $_POST['amount'] ?? 0;

// Fetch cart contents
$stmt = $conn->prepare("SELECT id, product_name, price FROM cart WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();

if (empty($items)) {
    http_response_code(400);
    exit('Cart is empty.');
}

// Log to orders
foreach ($items as $item) {
    $stmt = $conn->prepare("INSERT INTO orders (username, product_name, price, method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $username, $item['product_name'], $item['price'], $method);
    $stmt->execute();
}

// Fetch shipping info
$stmt = $conn->prepare("SELECT * FROM shipping_addresses WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$shipping = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Clear cart
$stmt = $conn->prepare("DELETE FROM cart WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->close();

// Compose HTML Email
$to_customer = $username;
$to_admin = "lambspunflorals@gmail.com"; // Change this to your real store email

$subject = "🧶 Your LambSpun Order Confirmation";
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: LambSpun Florals <no-reply@lambspunflorals.com>\r\n";

// Create order summary HTML
$summary = "";
foreach ($items as $item) {
    $summary .= "<li>{$item['product_name']} — $" . number_format($item['price'], 2) . "</li>";
}

$shippingBlock = "
<strong>Shipping Address:</strong><br>
{$shipping['name']}<br>
{$shipping['address']}<br>
{$shipping['city']}, {$shipping['state']} {$shipping['zip']}<br>
{$shipping['country']}
";

$email_body = "
<html>
<body style='font-family: Arial, sans-serif;'>
    <h2>🧵 Thank you for your order from LambSpun Florals!</h2>
    <p>We're thrilled to bring handmade beauty your way.</p>
    <p><strong>Payment Method:</strong> $method<br>
       <strong>Order Total:</strong> $" . number_format($amount, 2) . "</p>

    <h3>🧺 Items Ordered:</h3>
    <ul>$summary</ul>

    <h3>$shippingBlock</h3>

    <p>If you have any questions, just reply to this email. 🌸</p>
    <p><em>- The LambSpun Team</em></p>
</body>
</html>
";

// Send to customer
mail($to_customer, $subject, $email_body, $headers);

// Send to admin/store
mail($to_admin, "New LambSpun Order", $email_body, $headers);

// Redirect
header("Location: thankyou.php");
exit;
?>
