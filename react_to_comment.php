<?php
session_start();
include 'config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$commentId = (int) $data['comment_id'];
$type = $data['type']; // 'like' or 'dislike'

try {
    // Fetch current reactions
    $stmt = $pdo->prepare("SELECT reactions FROM comments WHERE id = ?");
    $stmt->execute([$commentId]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    $reactions = json_decode($comment['reactions'], true) ?? ['likes' => 0, 'dislikes' => 0];

    // Update reactions
    $reactions[$type] += 1;

    // Save updated reactions
    $stmt = $pdo->prepare("UPDATE comments SET reactions = ? WHERE id = ?");
    $stmt->execute([json_encode($reactions), $commentId]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>