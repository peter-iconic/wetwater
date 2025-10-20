<?php
// content_score_updater.php
// Run this regularly via cron: e.g. every 5 minutes
// crontab example: */5 * * * * /usr/bin/php /path/to/content_score_updater.php >/dev/null 2>&1

include 'config/db.php'; // $pdo (PDO)

// We'll compute a score for posts created in the last X days (for efficiency)
$lookback_days = 30;

// Fetch posts to update
$sql = "SELECT id, created_at FROM posts WHERE created_at >= NOW() - INTERVAL :days DAY";
$stmt = $pdo->prepare($sql);
$stmt->execute([':days' => $lookback_days]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepared statements for counts (performance)
$cntLikesStmt = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE post_id = ? AND type = 'like'");
$cntDislikesStmt = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE post_id = ? AND type = 'dislike'");
$cntCommentsStmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
$cntRepostsStmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE original_post_id = ?");
$cntViewsStmt = $pdo->prepare("SELECT COUNT(*) FROM user_behavior WHERE target_type='post' AND target_id = ? AND action_type='view' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$fetchRecentViewsStmt = $pdo->prepare("SELECT COUNT(*) FROM user_behavior WHERE target_type='post' AND target_id = ? AND action_type='view' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 HOUR)");

// Upsert statement for content_scores
$upsert = $pdo->prepare("
    INSERT INTO content_scores (post_id, engagement_score, virality_score, total_score, calculated_at)
    VALUES (:post_id, :engagement_score, :virality_score, :total_score, NOW())
    ON DUPLICATE KEY UPDATE engagement_score = :engagement_score_u, virality_score = :virality_score_u, total_score = :total_score_u, calculated_at = NOW()
");

foreach ($posts as $p) {
    $postId = $p['id'];

    $cntLikesStmt->execute([$postId]);
    $likes = (int) $cntLikesStmt->fetchColumn();

    $cntDislikesStmt->execute([$postId]);
    $dislikes = (int) $cntDislikesStmt->fetchColumn();

    $cntCommentsStmt->execute([$postId]);
    $comments = (int) $cntCommentsStmt->fetchColumn();

    $cntRepostsStmt->execute([$postId]);
    $reposts = (int) $cntRepostsStmt->fetchColumn();

    $cntViewsStmt->execute([$postId]);
    $views = (int) $cntViewsStmt->fetchColumn();

    $fetchRecentViewsStmt->execute([$postId]);
    $recentViews = (int) $fetchRecentViewsStmt->fetchColumn();

    // Engagement score: weighted combination
    $engagement_score = ($likes * 2.5) + ($comments * 4.0) + ($reposts * 5.0) + ($views * 0.7);

    // Virality: acceleration of views (recent / total) and reposts growth
    $virality = 0;
    if ($views > 0) {
        $virality = ($recentViews / max(1, $views)) * 100; // percent recent activity
    }
    // Small bump for reposts proportion
    $virality += min(50, $reposts * 8);

    // Penalty for dislikes
    $penalty = ($dislikes * 2.0);

    // Age decay — penalize older posts slightly (but keep highly viral posts high)
    $hours_old = (new DateTime())->diff(new DateTime($p['created_at']))->h
        + ((new DateTime())->diff(new DateTime($p['created_at']))->days * 24);
    $age_factor = pow(max(1, $hours_old / 24), 0.7); // older => bigger divisor

    // Base learned score
    $total_score = (($engagement_score * 1.0) + ($virality * 2.0) - ($penalty)) / $age_factor;

    // Normalize and clamp
    $total_score = round(max(0, min(9999, $total_score)), 2);

    // Bind and upsert
    $upsert->execute([
        ':post_id' => $postId,
        ':engagement_score' => $engagement_score,
        ':virality_score' => $virality,
        ':total_score' => $total_score,
        ':engagement_score_u' => $engagement_score,
        ':virality_score_u' => $virality,
        ':total_score_u' => $total_score
    ]);
}

// Optionally purge very old content_scores if you want
// $pdo->exec("DELETE FROM content_scores WHERE calculated_at < NOW() - INTERVAL 90 DAY");

echo "Content scores updated for " . count($posts) . " posts.\n";
