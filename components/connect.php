<?php
// Database configuration
$dbServer = 'localhost';
$dbUsername = 'root';
$dbPassword = 'root';
$dbName = 'shop_db';

try {
    $conn = new PDO("mysql:host=$dbServer;dbname=$dbName", $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>