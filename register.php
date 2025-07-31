<?php
require_once 'db.php';
// then use $conn as the database connection

// DB config
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'lambspun';

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "lambspun_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        echo "All fields are required.";
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Username or email already exists.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $username, $email, $hashed);

        if ($insert->execute()) {
            echo "🎉 Registration successful!";
        } else {
            echo "Error: " . $insert->error;
        }

        $insert->close();
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
<h2>Register New Account</h2>
<form method="POST" action="">
    Username: <br><input type="text" name="username" required><br><br>
    Email: <br><input type="email" name="email" required><br><br>
    Password: <br><input type="password" name="password" required><br><br>
    <button type="submit">Register</button>
</form>
</body>
</html>
