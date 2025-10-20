<?php
// react.php
session_start();
header('Content-Type: application/json');
include 'config/db.php'; // expects $pdo (PDO instance)

// Basic auth check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
$type = isset($_POST['type']) ? $_POST['type'] : ''; // 'like' or 'dislike'
$ajax = isset($_POST['ajax']) ? true : false;

if ($post_id <= 0 || !in_array($type, ['like', 'dislike'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Rate limiting: don't allow more than 10 reactions per 10 seconds (simple throttle)
    $limitStmt = $pdo->prepare("SELECT COUNT(*) FROM user_behavior WHERE user_id = :uid AND action_type = 'react' AND created_at >= (NOW() - INTERVAL 10 SECOND)");
    $limitStmt->execute([':uid' => $user_id]);
    if ((int) $limitStmt->fetchColumn() > 10) {
        echo json_encode(['success' => false, 'message' => 'Too many actions. Slow down.']);
        exit;
    }

    // Use transaction to avoid race conditions
    $pdo->beginTransaction();

    // Check existing reaction by this user on this post
    $check = $pdo->prepare("SELECT id, type FROM reactions WHERE post_id = :post_id AND user_id = :user_id LIMIT 1 FOR UPDATE");
    $check->execute([':post_id' => $post_id, ':user_id' => $user_id]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    $action = 'none';

    if ($existing) {
        if ($existing['type'] === $type) {
            // User clicked the same reaction: remove it (toggle off)
            $del = $pdo->prepare("DELETE FROM reactions WHERE id = :id");
            $del->execute([':id' => $existing['id']]);
            $action = 'removed';
        } else {
            // User switches reaction (like -> dislike or vice versa)
            $upd = $pdo->prepare("UPDATE reactions SET type = :type, created_at = NOW() WHERE id = :id");
            $upd->execute([':type' => $type, ':id' => $existing['id']]);
            $action = 'updated';
        }
    } else {
        // Insert new reaction
        $ins = $pdo->prepare("INSERT INTO reactions (post_id, user_id, type, created_at) VALUES (:post_id, :user_id, :type, NOW())");
        $ins->execute([':post_id' => $post_id, ':user_id' => $user_id, ':type' => $type]);
        $action = 'added';
    }

    // Record behavior
    $beh = $pdo->prepare("INSERT INTO user_behavior (user_id, action_type, target_id, target_type, created_at) VALUES (:uid, 'react', :target_id, 'post', NOW())");
    $beh->execute([':uid' => $user_id, ':target_id' => $post_id]);

    // Recalculate small metrics for this post and update content_scores (incremental)
    // We'll compute counts and write to content_scores
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

    // Recompute scores with the same formula as updater (fast)
    $engagement_score = ($likes * 2.5) + ($comments * 4.0) + ($reposts * 5.0) + ($views * 0.7);
    $virality = $views > 0 ? ($recentViews / max(1, $views)) * 100 : 0;
    $virality += min(50, $reposts * 8);
    $penalty = ($dislikes * 2.0);

    // Age factor
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

    // Upsert content_scores
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

    // Return updated counts and action
    echo json_encode([
        'success' => true,
        'action' => $action,
        'likes' => $likes,
        'dislikes' => $dislikes,
        'comments' => $comments,
        'reposts' => $reposts
    ]);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    error_log("react.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
