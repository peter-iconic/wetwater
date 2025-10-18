<?php
// config/db.php

// Database connection details
$host = 'localhost'; // Replace with your database host
$dbname = 'school_social_app'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = 'Mukomapeter1$'; // Replace with your database password

// Create a PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable exceptions for errors
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>