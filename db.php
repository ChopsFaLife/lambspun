<?php
// Database connection settings
// Update these variables to match your local MySQL credentials
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'lambspun';

// Create a new MySQLi connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check the connection
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Note: this file is included by other PHP scripts. Do not output any HTML or text here.