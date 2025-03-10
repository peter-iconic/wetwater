<?php
session_start();
include 'config/db.php';

// Start the session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit();
}

// Get the user ID to follow/unfollow
if (!isset($_POST['following_id']) || !is_numeric($_POST['following_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID.']);
    exit();
}
$following_id = intval($_POST['following_id']);

// Handle follow/unfollow action
if ($_POST['action'] === 'follow') {
    // Check if the user is already following
    $stmt = $pdo->prepare("SELECT * FROM followers WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$_SESSION['user_id'], $following_id]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['status' => 'error', 'message' => 'You are already following this user.']);
        exit();
    }

    // Follow the user
    $stmt = $pdo->prepare("INSERT INTO followers (follower_id, following_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $following_id]);
    echo json_encode(['status' => 'success', 'message' => 'Followed successfully.']);
} elseif ($_POST['action'] === 'unfollow') {
    // Unfollow the user
    $stmt = $pdo->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$_SESSION['user_id'], $following_id]);
    echo json_encode(['status' => 'success', 'message' => 'Unfollowed successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}
?>