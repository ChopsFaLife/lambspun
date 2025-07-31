<?php
session_start()

require_once 'db.php';
// then use $conn as the database connection

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli("localhost", "root", "", "lambspun_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$submitted = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = $_POST['state'];
    $zip = trim($_POST['zip']);
    $country = $_POST['country'];

    $stmt = $conn->prepare("REPLACE INTO shipping_addresses (username, name, address, city, state, zip, country) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $name, $address, $city, $state, $zip, $country);
    $stmt->execute();
    $stmt->close();

    $submitted = true;
}

// Fetch existing
$stmt = $conn->prepare("SELECT * FROM shipping_addresses WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$existing = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shipping Info</title>
</head>
<body>
<h2>📦 Shipping Information</h2>

<?php if ($submitted): ?>
    <p><strong>✅ Address saved!</strong></p>
<?php endif; ?>

<form method="POST" action="">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($existing['name'] ?? '') ?>" required><br><br>

    <label>Address:</label><br>
    <input type="text" name="address" value="<?= htmlspecialchars($existing['address'] ?? '') ?>" required><br><br>

    <label>City:</label><br>
    <input type="text" name="city" value="<?= htmlspecialchars($existing['city'] ?? '') ?>" required><br><br>

    <label>State:</label><br>
    <select name="state" required>
        <option value="">Select State</option>
        <?php
        $states = ["AR" => "Arkansas", "TX" => "Texas", "OK" => "Oklahoma", "MO" => "Missouri"];
        foreach ($states as $abbr => $full) {
            $selected = ($existing['state'] ?? '') === $abbr ? 'selected' : '';
            echo "<option value=\"$abbr\" $selected>$full</option>";
        }
        ?>
    </select><br><br>

    <label>ZIP Code:</label><br>
    <input type="text" name="zip" value="<?= htmlspecialchars($existing['zip'] ?? '') ?>" required><br><br>

    <label>Country:</label><br>
    <select name="country" required>
        <option value="USA" <?= ($existing['country'] ?? '') === "USA" ? "selected" : "" ?>>USA</option>
        <option value="Canada" <?= ($existing['country'] ?? '') === "Canada" ? "selected" : "" ?>>Canada</option>
        <option value="Australia" <?= ($existing['country'] ?? '') === "Australia" ? "selected" : "" ?>>Australia</option>
    </select><br><br>

    <button type="submit">Save Shipping Info</button>
</form>

<br><a href="account.php">⬅ Back to Account</a>
</body>
</html>
