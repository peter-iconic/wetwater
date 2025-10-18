<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $videoId = $data['video_id'];
    $type = $data['type'];
    $userId = 1; // Replace with the logged-in user's ID

    try {
        // Check if the user has already reacted
        $stmt = $pdo->prepare("SELECT * FROM reactions WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$videoId, $userId]);
        $existingReaction = $stmt->fetch();

        if ($existingReaction) {
            // Update existing reaction
            $stmt = $pdo->prepare("UPDATE reactions SET type = ? WHERE id = ?");
            $stmt->execute([$type, $existingReaction['id']]);
        } else {
            // Insert new reaction
            $stmt = $pdo->prepare("INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, ?)");
            $stmt->execute([$videoId, $userId, $type]);
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>