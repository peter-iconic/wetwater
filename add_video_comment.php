<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['video_id'] ?? $_POST['post_id'] ?? null;
    $type = $_POST['type'] ?? null;
    $content = $_POST['content'] ?? null;
    $userId = 1; // Replace with the logged-in user's ID

    if (!$postId || !$type || !$content) {
        die("Invalid input.");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$postId, $userId, $content, $type]);

        // Redirect back to the comments page
        if ($type === 'video') {
            header("Location: comments.php?video_id=$postId");
        } else {
            header("Location: comments.php?post_id=$postId");
        }
        exit();
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>