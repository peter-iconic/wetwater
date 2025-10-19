<?php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $token = $_POST['token'] ?? '';
    $phone = $_POST['phone'] ?? '';

    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    // Hash the new password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    if (!empty($token)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :token AND token_expiry > NOW() LIMIT 1");
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif (!empty($phone)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = :phone AND reset_token = :otp AND token_expiry > NOW() LIMIT 1");
        $stmt->execute([
            'phone' => $phone,
            'otp' => $_POST['otp'] ?? ''
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        die("Invalid request.");
    }

    if (!$user)
        die("Invalid or expired token/OTP.");

    // Update password and clear token
    $update = $pdo->prepare("UPDATE users SET password = :pass, reset_token = NULL, token_expiry = NULL WHERE id = :id");
    $update->execute([
        'pass' => $passwordHash,
        'id' => $user['id']
    ]);

    echo "Password updated successfully. <a href='login.php'>Login</a>";
}
