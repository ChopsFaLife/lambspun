<?php

session_start();

require_once 'db.php';
// then use $conn as the database connection

session_unset();    // Unset all session variables
session_destroy();  // Destroy the session

// Optional: Redirect to login page
header("Location: login.php");
exit;
?>
