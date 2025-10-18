<?php
session_start();
include 'config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$commentId = (int) $data['comment_id'];
$content = $data['content'];

try {
    $stmt = $pdo->prepare("UPDATE comments SET content = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$content, $commentId, $_SESSION['user_id']]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>