<?php
session_start();
include 'config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$postId = (int) $data['post_id']; // Can be a video_id or post_id
$content = $data['content'];
$parentCommentId = $data['parent_comment_id'] ?? null;
$type = $data['type'] ?? 'post'; // Default to 'post' if not provided

try {
    $stmt = $pdo->prepare("
        INSERT INTO comments (post_id, user_id, content, type, parent_comment_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$postId, $_SESSION['user_id'], $content, $type, $parentCommentId]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>