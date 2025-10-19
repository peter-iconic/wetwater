<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please log in to react']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;
    $type = isset($_POST['type']) ? $_POST['type'] : '';

    if (!$post_id || !in_array($type, ['like', 'dislike'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    try {
        // Check if user already reacted
        $stmt = $pdo->prepare("SELECT id, type FROM reactions WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
        $existing_reaction = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_reaction) {
            if ($existing_reaction['type'] === $type) {
                // Remove reaction
                $stmt = $pdo->prepare("DELETE FROM reactions WHERE id = ?");
                $stmt->execute([$existing_reaction['id']]);
                $action = 'removed';
            } else {
                // Update reaction
                $stmt = $pdo->prepare("UPDATE reactions SET type = ? WHERE id = ?");
                $stmt->execute([$type, $existing_reaction['id']]);
                $action = 'updated';
            }
        } else {
            // Insert new reaction
            $stmt = $pdo->prepare("INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $type]);
            $action = 'added';

            // Add points for reaction
            addUserPoints($pdo, $user_id, 2, 'reacted_to_post');
        }

        // Get updated counts
        $stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM reactions WHERE post_id = ? AND type = 'like'");
        $stmt->execute([$post_id]);
        $like_count = $stmt->fetch(PDO::FETCH_ASSOC)['like_count'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as dislike_count FROM reactions WHERE post_id = ? AND type = 'dislike'");
        $stmt->execute([$post_id]);
        $dislike_count = $stmt->fetch(PDO::FETCH_ASSOC)['dislike_count'];

        // Send notification to post owner if it's a like
        if ($type === 'like' && $action === 'added') {
            $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $post_owner = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($post_owner && $post_owner['user_id'] != $user_id) {
                $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $username = $stmt->fetch(PDO::FETCH_ASSOC)['username'];

                $message = "$username liked your post";
                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
                $stmt->execute([$post_owner['user_id'], $message]);
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'action' => $action,
            'likes' => $like_count,
            'dislikes' => $dislike_count
        ]);

    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function addUserPoints($pdo, $user_id, $points, $reason)
{
    try {
        // Update user points
        $stmt = $pdo->prepare("INSERT INTO user_points (user_id, points) VALUES (?, ?) ON DUPLICATE KEY UPDATE points = points + VALUES(points)");
        $stmt->execute([$user_id, $points]);

        // Log points
        $stmt = $pdo->prepare("INSERT INTO points_log (user_id, points, reason) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $points, $reason]);
    } catch (PDOException $e) {
        // Silently fail for points, don't break the main functionality
    }
}
?>