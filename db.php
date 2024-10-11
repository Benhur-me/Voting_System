<?php

$host = 'localhost';
$dbname = 'voting_system';
$username = 'root';
$password = '';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Output error and stop the script if the connection fails
    die("Database connection failed: " . $e->getMessage());
}
?>
