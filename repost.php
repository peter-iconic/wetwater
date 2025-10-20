<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please log in to repost']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;

    if (!$post_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    try {
        // Check if user already reposted this post
        $stmt = $pdo->prepare("SELECT id FROM posts WHERE user_id = ? AND original_post_id = ?");
        $stmt->execute([$user_id, $post_id]);
        $existing_repost = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_repost) {
            // Remove repost
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$existing_repost['id']]);
            $action = 'removed';
        } else {
            // Create repost
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, original_post_id, created_at) VALUES (?, '', ?, NOW())");
            $stmt->execute([$user_id, $post_id]);
            $action = 'added';

            // Add points for reposting
            addUserPoints($pdo, $user_id, 3, 'reposted_content');

            // Send notification to original poster
            $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $original_post = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($original_post && $original_post['user_id'] != $user_id) {
                $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $username = $stmt->fetch(PDO::FETCH_ASSOC)['username'];

                $message = "$username reposted your post";
                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
                $stmt->execute([$original_post['user_id'], $message]);
            }
        }

        // Get updated repost count
        $stmt = $pdo->prepare("SELECT COUNT(*) as repost_count FROM posts WHERE original_post_id = ?");
        $stmt->execute([$post_id]);
        $repost_count = $stmt->fetch(PDO::FETCH_ASSOC)['repost_count'];

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'action' => $action,
            'repost_count' => $repost_count
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
        // Silently fail for points
    }
}
?>