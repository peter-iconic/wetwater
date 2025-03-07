<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to react.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $type = $_POST['type']; // 'like' or 'dislike'
    $user_id = $_SESSION['user_id'];

    // Check if the user has already reacted to this post
    $stmt = $pdo->prepare("SELECT * FROM reactions WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $existing_reaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_reaction) {
        // If the user is trying to react the same way again, remove the reaction
        if ($existing_reaction['type'] === $type) {
            $stmt = $pdo->prepare("DELETE FROM reactions WHERE id = ?");
            $stmt->execute([$existing_reaction['id']]);
        } else {
            // Update the reaction type
            $stmt = $pdo->prepare("UPDATE reactions SET type = ? WHERE id = ?");
            $stmt->execute([$type, $existing_reaction['id']]);
        }
    } else {
        // Insert a new reaction
        $stmt = $pdo->prepare("INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $type]);
    }

    // Fetch updated reaction counts
    $stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM reactions WHERE post_id = ? AND type = 'like'");
    $stmt->execute([$post_id]);
    $like_count = $stmt->fetch(PDO::FETCH_ASSOC)['like_count'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as dislike_count FROM reactions WHERE post_id = ? AND type = 'dislike'");
    $stmt->execute([$post_id]);
    $dislike_count = $stmt->fetch(PDO::FETCH_ASSOC)['dislike_count'];

    echo json_encode([
        'status' => 'success',
        'like_count' => $like_count,
        'dislike_count' => $dislike_count
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>