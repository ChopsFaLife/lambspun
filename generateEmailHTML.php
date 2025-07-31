<?php
function generateEmailHTML($orderItems, $shippingInfo, $totalAmount) {
    // Build order item table rows
    $rows = '';
    foreach ($orderItems as $item) {
        $name = htmlspecialchars($item['name']);
        $qty = (int)$item['quantity'];
        $price = number_format($item['price'], 2);
        $rows .= "<tr>
                    <td style='padding:8px; border-bottom:1px solid #ddd;'>$name</td>
                    <td style='padding:8px; border-bottom:1px solid #ddd;'>$qty</td>
                    <td style='padding:8px; border-bottom:1px solid #ddd;'>\$$price</td>
                  </tr>";
    }

    // Format shipping address
    $shippingAddress = htmlspecialchars($shippingInfo['name']) . "<br>" .
                       htmlspecialchars($shippingInfo['address']) . "<br>" .
                       htmlspecialchars($shippingInfo['city']) . ", " .
                       htmlspecialchars($shippingInfo['state']) . " " .
                       htmlspecialchars($shippingInfo['zip']) . "<br>" .
                       htmlspecialchars($shippingInfo['country']);

    // Return full HTML
    return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fdfdfd;
            color: #444;
            padding: 20px;
        }
        .container {
            max-width: 650px;
            margin: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .header {
            background: #f8f1e9;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-width: 220px;
            height: auto;
        }
        h2 {
            color: #7d4f50;
        }
        .details {
            padding: 20px;
        }
        .footer {
            background: #f8f1e9;
            color: #777;
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #f0dfd6;
            padding: 10px;
            text-align: left;
            color: #7d4f50;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <img src='https://lambspun.infinityfree.me/images/lambspun-banner.png' alt='LambSpun Florals Logo'>
        </div>
        <div class='details'>
            <h2>Thank you for your order!</h2>
            <p>We’ve received your order and it’s now being processed. You’ll receive another email when it ships.</p>

            <h3>Shipping Information</h3>
            <p>$shippingAddress</p>

            <h3>Order Summary</h3>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
                $rows
                <tr>
                    <td colspan='2' style='text-align:right; padding:10px; font-weight:bold;'>Total:</td>
                    <td style='padding:10px; font-weight:bold;'>\$" . number_format($totalAmount, 2) . "</td>
                </tr>
            </table>
        </div>
        <div class='footer'>
            Handcrafted with heart in Fort Smith, AR – LambSpun Florals
        </div>
    </div>
</body>
</html>";
}
