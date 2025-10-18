<?php
// Include the database connection
include 'config/db.php';

// Include the Composer autoloader
require 'vendor/autoload.php';

use phpseclib3\Crypt\RSA;

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

    // Generate a new private/public key pair using phpseclib
    try {
        $rsa = RSA::createKey(2048); // 2048-bit key

        // Extract the private key
        $privateKey = $rsa->toString('PKCS8');

        // Extract the public key
        $publicKey = $rsa->getPublicKey()->toString('PKCS8');
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to generate encryption keys. Please try again.";
        header("Location: register.php");
        exit();
    }

    // Insert the new user into the database with public and private keys
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, public_key, private_key) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password, $publicKey, $privateKey]);

        // Fetch the newly created user's details
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $newUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($newUser) {
            // Set session variables
            $_SESSION['user_id'] = $newUser['id'];
            $_SESSION['username'] = $newUser['username'];
            $_SESSION['email'] = $newUser['email'];
            $_SESSION['is_admin'] = $newUser['is_admin']; // Set admin status
            $_SESSION['public_key'] = $newUser['public_key']; // Store public key in session
            $_SESSION['private_key'] = $newUser['private_key']; // Store private key in session

            // Set success message
            $_SESSION['success'] = "Registration successful! You are now logged in.";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to fetch user details after registration.";
            header("Location: register.php");
            exit();
        }
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