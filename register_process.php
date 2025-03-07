<?php
// Include the database connection
include 'config/db.php';

// Start the session (for storing success/error messages)
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: register.php");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: register.php");
        exit();
    }

    // Check if email or username already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['error'] = "Email or username already exists.";
        header("Location: register.php");
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);

        // Set success message
        $_SESSION['success'] = "Registration successful! You can now login.";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: register.php");
        exit();
    }
} else {
    // Redirect if the form is not submitted
    header("Location: register.php");
    exit();
}