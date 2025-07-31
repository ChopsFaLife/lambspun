<?php

session_start();

require_once 'db.php';
// then use $conn as the database connection

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Account</title>
	<Link rel="stylesheets" href="style.css">
</head>
<body>
<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

<p><a href="https://lambspun.infinityfree.me/shop.html" target="_blank">Go to the LambSpun Shop 🧶</a></p>
<p><a href="logout.php">Logout</a></p>

<form action="log_order.php" method="POST">
    <h3>Simulate Purchase</h3>
    <label>Product Name:</label><br>
    <input type="text" name="product_name" value="Lavender Bundle" required><br><br>

    <label>Price:</label><br>
    <input type="number" name="price" step="0.01" value="25.00" required><br><br>

    <button type="submit">Log Purchase</button>
</form>

<hr>


<hr>

<h3>Your Purchase History</h3>
<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "lambspun_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = $_SESSION['username'];
$sql = "SELECT product_name, price, order_time FROM orders WHERE username = ? ORDER BY order_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['product_name']} — \${$row['price']} on {$row['purchased_at']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No purchases yet.</p>";
}

$stmt->close();
$conn->close();
?>
</body>
</html>
