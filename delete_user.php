<?php
include 'config/db.php';

// Redirect if the user is not an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Check if a user ID is provided
if (!isset($_GET['id'])) {
    die("User ID not specified.");
}

$user_id = $_GET['id'];

// Delete the user
try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    $_SESSION['success'] = "User deleted successfully!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Failed to delete user. Please try again.";
}

header("Location: admin_users.php");
exit();
?>