<?php
// add_comment.php
session_start();
header('Content-Type: application/json');
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
$parent_comment_id = isset($_POST['parent_comment_id']) ? (int) $_POST['parent_comment_id'] : null;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
// Basic sanitation: limit length
if ($post_id <= 0 || $content === '' || mb_strlen($content) > 2000) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    // Rate limiting: not more than 5 comments in 10 seconds
    $limitStmt = $pdo->prepare("SELECT COUNT(*) FROM user_behavior WHERE user_id = :uid AND action_type = 'comment' AND created_at >= (NOW() - INTERVAL 10 SECOND)");
    $limitStmt->execute([':uid' => $user_id]);
    if ((int) $limitStmt->fetchColumn() > 5) {
        echo json_encode(['success' => false, 'message' => 'Too many actions. Slow down.']);
        exit;
    }

    $pdo->beginTransaction();

    // If parent_comment_id provided, ensure it exists and belongs to same post (defend against mismatches)
    if ($parent_comment_id) {
        $chk = $pdo->prepare("SELECT post_id FROM comments WHERE id = :cid LIMIT 1");
        $chk->execute([':cid' => $parent_comment_id]);
        $res = $chk->fetch(PDO::FETCH_ASSOC);
        if (!$res || (int) $res['post_id'] !== $post_id) {
            // Invalid parent comment
            $parent_comment_id = null;
        }
    }

    // Insert comment
    $ins = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, created_at, parent_comment_id, type) VALUES (:post_id, :user_id, :content, NOW(), :parent_comment_id, 'post')");
    $ins->execute([
        ':post_id' => $post_id,
        ':user_id' => $user_id,
        ':content' => $content,
        ':parent_comment_id' => $parent_comment_id
    ]);
    $comment_id = (int) $pdo->lastInsertId();

    // Record behavior
    $beh = $pdo->prepare("INSERT INTO user_behavior (user_id, action_type, target_id, target_type, created_at) VALUES (:uid, 'comment', :target_id, 'post', NOW())");
    $beh->execute([':uid' => $user_id, ':target_id' => $post_id]);

    // Recalculate metrics & update content_scores
    $counts = $pdo->prepare("
        SELECT
            (SELECT COUNT(*) FROM reactions WHERE post_id = :post_id AND type = 'like') AS likes,
            (SELECT COUNT(*) FROM reactions WHERE post_id = :post_id AND type = 'dislike') AS dislikes,
            (SELECT COUNT(*) FROM comments WHERE post_id = :post_id) AS comments,
            (SELECT COUNT(*) FROM posts r WHERE r.original_post_id = :post_id) AS reposts,
            (SELECT COUNT(*) FROM user_behavior ub WHERE ub.target_type='post' AND ub.target_id = :post_id AND ub.action_type='view' AND ub.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS views,
            (SELECT COUNT(*) FROM user_behavior ub2 WHERE ub2.target_type='post' AND ub2.target_id = :post_id AND ub2.action_type='view' AND ub2.created_at >= DATE_SUB(NOW(), INTERVAL 6 HOUR)) AS recent_views
    ");
    $counts->execute([':post_id' => $post_id]);
    $c = $counts->fetch(PDO::FETCH_ASSOC);

    $likes = (int) $c['likes'];
    $dislikes = (int) $c['dislikes'];
    $comments = (int) $c['comments'];
    $reposts = (int) $c['reposts'];
    $views = (int) $c['views'];
    $recentViews = (int) $c['recent_views'];

    $engagement_score = ($likes * 2.5) + ($comments * 4.0) + ($reposts * 5.0) + ($views * 0.7);
    $virality = $views > 0 ? ($recentViews / max(1, $views)) * 100 : 0;
    $virality += min(50, $reposts * 8);
    $penalty = ($dislikes * 2.0);

    $ageStmt = $pdo->prepare("SELECT created_at FROM posts WHERE id = :post_id LIMIT 1");
    $ageStmt->execute([':post_id' => $post_id]);
    $created_at = $ageStmt->fetchColumn();
    $hours_old = 1;
    if ($created_at) {
        $created = new DateTime($created_at);
        $now = new DateTime();
        $hours_old = max(1, ($now->getTimestamp() - $created->getTimestamp()) / 3600);
    }
    $age_factor = pow(max(1, $hours_old / 24), 0.7);

    $total_score = (($engagement_score * 1.0) + ($virality * 2.0) - ($penalty)) / $age_factor;
    $total_score = round(max(0, min(9999, $total_score)), 2);

    $upsert = $pdo->prepare("
        INSERT INTO content_scores (post_id, engagement_score, virality_score, total_score, calculated_at)
        VALUES (:post_id, :engagement_score, :virality_score, :total_score, NOW())
        ON DUPLICATE KEY UPDATE engagement_score = :engagement_score_u, virality_score = :virality_score_u, total_score = :total_score_u, calculated_at = NOW()
    ");
    $upsert->execute([
        ':post_id' => $post_id,
        ':engagement_score' => $engagement_score,
        ':virality_score' => $virality,
        ':total_score' => $total_score,
        ':engagement_score_u' => $engagement_score,
        ':virality_score_u' => $virality,
        ':total_score_u' => $total_score
    ]);

    $pdo->commit();

    // Optionally return the newly rendered comment fragment (you can also return counts only)
    // For now return counts and comment id
    echo json_encode([
        'success' => true,
        'comment_id' => $comment_id,
        'comments' => $comments,
        'likes' => $likes,
        'reposts' => $reposts
    ]);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    error_log("add_comment.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
