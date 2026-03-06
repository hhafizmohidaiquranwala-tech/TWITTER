<?php
$host = 'localhost';
$username = 'rsoa_rsoa378_41';
$password = '123456';
$database = 'rsoa_rsoa378_41';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
