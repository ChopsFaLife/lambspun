<?php

session_start();

require_once 'db.php';
// then use $conn as the database connection

// DB Config
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'lambspun';

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo "Please fill in all fields.";
        exit;
    }

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

if ($stmt->num_rows === 1) {
    // Verify password
    $stmt->bind_result($id, $username, $email, $hashed);
    $stmt->fetch();

    if (password_verify($password, $hashed)) {
        // Set session variables
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Invalid password.";
    }
}
 else {
        echo "❌ No such user found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>User Login</h2>
<form method="POST" action="">
    Username: <br><input type="text" name="username" required><br><br>
    Password: <br><input type="password" name="password" required><br><br>

    <label>
        <input type="checkbox" name="remember"> Remember Me
    </label>
    <br><br>

    <button type="submit">Login</button>
</form>
</body>
</html>
