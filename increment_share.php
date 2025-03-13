<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $videoId = $data['video_id'];

    try {
        // Increment the share count
        $stmt = $pdo->prepare("UPDATE videos SET shares = shares + 1 WHERE id = ?");
        $stmt->execute([$videoId]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>