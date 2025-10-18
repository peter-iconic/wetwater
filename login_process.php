<?php
session_start();

// Include the database connection
include 'config/db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: login.php");
        exit();
    }

    // Fetch user from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists and password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin']; // Set admin status
        $_SESSION['public_key'] = $user['public_key']; // Store public key in session
        $_SESSION['private_key'] = $user['private_key']; // Store private key in session

        // Redirect to dashboard or homepage
        if ($user['is_admin']) {
            header("Location: admin.php"); // Redirect admins to the admin dashboard
        } else {
            header("Location: index.php"); // Redirect regular users to the homepage
        }
        exit();
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
    }
} else {
    // Redirect to login page if the form is not submitted
    header("Location: login.php");
    exit();
}