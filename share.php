<?php
session_start();
include 'config/db.php';

if (!isset($_GET['post_id'])) {
    header("Location: index.php");
    exit();
}

$post_id = intval($_GET['post_id']);

// Fetch post details to verify it exists
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.username 
        FROM posts p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header("Location: index.php");
        exit();
    }

    // Track the share (if user is logged in)
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("UPDATE posts SET shares = shares + 1 WHERE id = ?");
        $stmt->execute([$post_id]);

        // Also log in user_behavior table
        $stmt = $pdo->prepare("INSERT INTO user_behavior (user_id, action_type, target_id, target_type) VALUES (?, 'share', ?, 'post')");
        $stmt->execute([$_SESSION['user_id'], $post_id]);
    }

} catch (PDOException $e) {
    // Continue anyway, don't break the redirect
}

// Redirect to index.php with the specific post highlighted
header("Location: index.php?shared_post=" . $post_id);
exit();
?>