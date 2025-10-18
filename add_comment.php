<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $content = trim($_POST['content']);
    $parent_comment_id = isset($_POST['parent_comment_id']) ? $_POST['parent_comment_id'] : null;
    $user_id = $_SESSION['user_id'];

    if (empty($content)) {
        $_SESSION['error'] = "Comment content cannot be empty.";
        header("Location: index.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, parent_comment_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $content, $parent_comment_id]);

        $_SESSION['success'] = "Comment added successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to add comment. Please try again.";
    }

    header("Location: index.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: index.php");
    exit();
}
?>