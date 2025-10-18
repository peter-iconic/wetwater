<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to share a post.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $caption = trim($_POST['caption']);
    $user_id = $_SESSION['user_id'];

    // Fetch the original post
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $original_post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$original_post) {
        echo json_encode(['status' => 'error', 'message' => 'Post not found.']);
        exit();
    }

    // Insert the shared post with the caption
    try {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, image, original_post_id, caption) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $original_post['content'],
            $original_post['image'],
            $original_post['id'], // Reference to the original post
            $caption // Caption added by the user
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Post shared successfully!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to share post. Please try again.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>