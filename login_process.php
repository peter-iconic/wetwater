<?php
session_start();
// Include the database connection
include 'config/db.php';

// Start the session

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: login.php");
        exit();
    }

    // Fetch user from database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists and password is correct
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        // Redirect to dashboard or homepage
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
