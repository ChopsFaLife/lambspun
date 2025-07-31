<?php
session_start();

// Block access if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Welcome</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>You are logged in with the email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
