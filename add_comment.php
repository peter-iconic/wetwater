<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please log in to comment']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;
    $parent_comment_id = isset($_POST['parent_comment_id']) ? intval($_POST['parent_comment_id']) : null;
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    if (!$content) {
        echo json_encode(['success' => false, 'message' => 'Comment content is required']);
        exit();
    }

    // If it's a reply, we need to get the post_id from the parent comment
    if ($parent_comment_id && !$post_id) {
        $stmt = $pdo->prepare("SELECT post_id FROM comments WHERE id = ?");
        $stmt->execute([$parent_comment_id]);
        $parent_comment = $stmt->fetch(PDO::FETCH_ASSOC);
        $post_id = $parent_comment['post_id'];
    }

    if (!$post_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid post']);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    try {
        // Insert comment
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, parent_comment_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $content, $parent_comment_id]);

        // Add points for commenting
        addUserPoints($pdo, $user_id, 5, 'commented_on_post');

        // Send notification to post owner
        if (!$parent_comment_id) { // Only for top-level comments
            $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $post_owner = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($post_owner && $post_owner['user_id'] != $user_id) {
                $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $username = $stmt->fetch(PDO::FETCH_ASSOC)['username'];

                $message = "$username commented on your post: " . (strlen($content) > 50 ? substr($content, 0, 50) . '...' : $content);
                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
                $stmt->execute([$post_owner['user_id'], $message]);
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Comment added successfully']);

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