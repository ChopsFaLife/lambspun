<?php
session_start();
require 'db.php';
require_once 'includes/send_email.php';

// FOR TESTING ONLY — define fake checkout data right now:
$_SESSION['checkout'] = [
    'user' => 'testuser',
    'email' => 'sarahashlamb@gmail.com',  // Replace this with your real inbox
    'items' => [
        ['name' => 'Lavender Bundle', 'qty' => 1, 'price' => 25],
        ['name' => '5 Rose Bundle', 'qty' => 1, 'price' => 45]
    ],
    'shipping' => [
        'name' => 'Chris Lamb',
        'address' => '123 Yarn St',
        'city' => 'Fayetteville',
        'state' => 'AR',
        'zip' => '72701',
        'country' => 'USA'
    ],
    'total' => 70
];

$user = $_SESSION['checkout']['user'] ?? null;
$items = $_SESSION['checkout']['items'] ?? [];
$shipping = $_SESSION['checkout']['shipping'] ?? [];
$total = $_SESSION['checkout']['total'] ?? 0;

if (!$items || !$shipping) {
    die("Missing order data.");
}

// Build item list
$itemRows = '';
foreach ($items as $item) {
    $itemRows .= "<tr><td>{$item['name']}</td><td>{$item['qty']}</td><td>\${$item['price']}</td></tr>";
}

// Build email HTML
$email_html = "
<html>
<head><style>
  table { border-collapse: collapse; width: 100%; }
  th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
</style></head>
<body>
  <h2>Thank you for your order from LambSpun Florals!</h2>
  <table>
    <tr><th>Item</th><th>Quantity</th><th>Price</th></tr>
    $itemRows
  </table>
  <p><strong>Total:</strong> \$$total</p>
  <h3>Shipping To:</h3>
  <p>
    {$shipping['name']}<br>
    {$shipping['address']}<br>
    {$shipping['city']}, {$shipping['state']} {$shipping['zip']}<br>
    {$shipping['country']}
  </p>
</body>
</html>
";

// Send to customer + admin
$to = $_SESSION['checkout']['email'];
$success1 = sendOrderEmail($to, "LambSpun Order Confirmation", $email_html);
$success2 = sendOrderEmail("sarahasheighc@gmail.com", "NEW ORDER - $user", $email_html);
$success3 = sendOrderEmail("orders@lambspunflorals.com", "NEW ORDER - $user", $email_html);

// Debug output
if ($success1 && $success2 && $success3) {
    echo "✅ Test emails sent successfully!";
} else {
    echo "❌ Failed to send one or both emails. Check your error logs.";
}
