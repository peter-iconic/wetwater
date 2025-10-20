<?php
// index.php
session_start();
include 'config/db.php'; // must provide $pdo (PDO instance)

// --- Utility: determine if this is CLI call for updater ---
if (php_sapi_name() === 'cli' && isset($argv[1]) && $argv[1] === 'update_scores') {
    // Run content score updater from CLI (cron can call: php /path/to/index.php update_scores)
    content_score_updater($pdo);
    exit;
}

// If called via web with action param, handle AJAX endpoints (load_posts)
$action = isset($_GET['action']) ? $_GET['action'] : null;
if ($action === 'load_posts') {
    header('Content-Type: application/json');
    session_start(); // ensure session available for AJAX
    $is_logged_in = isset($_SESSION['user_id']);
    $user_id = $is_logged_in ? $_SESSION['user_id'] : null;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = 10;

    $posts = getPersonalizedFeed($pdo, $user_id, $page, $limit);

    // Track view user_behavior for each returned post
    if ($user_id && !empty($posts)) {
        $ins = $pdo->prepare("INSERT INTO user_behavior (user_id, action_type, target_id, target_type, created_at) VALUES (?, 'view', ?, 'post', NOW())");
        foreach ($posts as $p) {
            try {
                $ins->execute([$user_id, $p['id']]);
            } catch (Exception $e) {
                // ignore insertion errors to not break AJAX
            }
        }
    }

    // Build HTML fragment (same markup as main page items)
    ob_start();
    foreach ($posts as $post): ?>
        <div class="feed-item" data-post-id="<?php echo $post['id']; ?>">
            <div class="post-header">
                <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" class="user-avatar"
                    alt="<?php echo htmlspecialchars($post['username']); ?>" onerror="this.src='default_profile.jpg'">
                <div class="user-info">
                    <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="username">
                        <?php echo htmlspecialchars($post['username']); ?>
                        <?php if (!empty($post['total_score']) ? $post['total_score'] : (isset($post['learned_score']) ? $post['learned_score'] : 0) > 80): ?>
                            <span class="trending-badge">TRENDING</span>
                        <?php endif; ?>
                    </a>
                    <div class="post-time">
                        <?php echo (new DateTime($post['created_at']))->format('M j, Y H:i'); ?>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-three-dots"></i>
                </button>
            </div>

            <div class="post-content">
                <?php if (!empty($post['image'])): ?>
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" class="post-image" alt="Post image"
                        onclick="toggleImageSize(this)" onerror="this.style.display='none'">
                <?php endif; ?>

                <?php if (!empty($post['content'])): ?>
                    <div class="post-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
                <?php endif; ?>

                <?php if (!empty($post['caption'])): ?>
                    <div class="post-text text-muted"><small><?php echo nl2br(htmlspecialchars($post['caption'])); ?></small></div>
                <?php endif; ?>
            </div>

            <div class="post-actions">
                <button class="action-btn like-btn <?php echo !empty($post['user_liked']) ? 'liked' : ''; ?>"
                    onclick="reactToPost(<?php echo $post['id']; ?>, 'like')" <?php echo isset($_SESSION['user_id']) ? '' : 'disabled'; ?>>
                    <i
                        class="bi bi-heart<?php echo !empty($post['user_liked']) ? '-fill' : ''; ?>"></i><?php echo (isset($post['likes']) ? $post['likes'] : 0); ?>
                </button>

                <button class="action-btn" onclick="toggleComments(<?php echo $post['id']; ?>)" <?php echo isset($_SESSION['user_id']) ? '' : 'disabled'; ?>>
                    <i class="bi bi-chat"></i> <?php echo (isset($post['comments']) ? $post['comments'] : 0); ?>
                </button>

                <button class="action-btn repost-btn" onclick="repost(<?php echo $post['id']; ?>)" <?php echo isset($_SESSION['user_id']) ? '' : 'disabled'; ?>>
                    <i class="bi bi-arrow-repeat"></i>
                    <span class="repost-count"><?php echo (isset($post['reposts']) ? $post['reposts'] : 0); ?></span>
                </button>

                <button class="action-btn" onclick="sharePost(<?php echo $post['id']; ?>)" <?php echo isset($_SESSION['user_id']) ? '' : 'disabled'; ?>>
                    <i class="bi bi-share"></i>
                </button>
            </div>

            <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display: none;">
                <div class="comments-container" id="comments-container-<?php echo $post['id']; ?>"></div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="add-comment-form-container">
                        <form class="add-comment-form" data-post-id="<?php echo $post['id']; ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Write a comment..." required>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach;
    $html = ob_get_clean();

    $resp = [
        'posts' => $posts,
        'html' => $html,
        'hasMore' => count($posts) >= $limit
    ];
    echo json_encode($resp);
    exit;
}

// =========================
// Normal page render below
// =========================

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Get current page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$posts = getPersonalizedFeed($pdo, $user_id, $page, 10);

// Track view for recommendation algorithm (insert user_behavior records for each loaded post)
if ($user_id && !empty($posts)) {
    $ins = $pdo->prepare("INSERT INTO user_behavior (user_id, action_type, target_id, target_type, created_at) VALUES (?, 'view', ?, 'post', NOW())");
    foreach ($posts as $post) {
        try {
            $ins->execute([$user_id, $post['id']]);
        } catch (Exception $e) {
            // ignore
        }
    }
}

// ---------------------
// FUNCTIONS
// ---------------------

/**
 * World-class personalized feed function.
 * Uses: content_scores, user_behavior, followers, post_tags, user_preferences.
 */
function getPersonalizedFeed($pdo, $user_id = null, $page = 1, $limit = 10)
{
    $offset = ($page - 1) * $limit;
    $limit = (int) $limit;
    $offset = (int) $offset;

    if ($user_id) {
        $sql = "
            SELECT
                p.*,
                u.username,
                u.profile_picture,
                COALESCE(cs.total_score, 50) AS learned_score,
                (SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'like') AS likes,
                (SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'dislike') AS dislikes,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments,
                (SELECT COUNT(*) FROM posts r WHERE r.original_post_id = p.id) AS reposts,
                (SELECT COUNT(*) FROM user_behavior ub WHERE ub.target_type = 'post' AND ub.target_id = p.id AND ub.action_type = 'view') AS views,
                EXISTS(SELECT 1 FROM reactions WHERE post_id = p.id AND user_id = :uid AND type = 'like') AS user_liked,
                EXISTS(SELECT 1 FROM reactions WHERE post_id = p.id AND user_id = :uid AND type = 'dislike') AS user_disliked,
                TIMESTAMPDIFF(HOUR, p.created_at, NOW()) AS hours_old
            FROM posts p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN content_scores cs ON cs.post_id = p.id
            WHERE p.original_post_id IS NULL
            ORDER BY
                (
                    (COALESCE(cs.total_score, 50) * 1.0) +
                    ((SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'like') * 2.8) +
                    ((SELECT COUNT(*) FROM comments WHERE post_id = p.id) * 3.6) +
                    ((SELECT COUNT(*) FROM posts r WHERE r.original_post_id = p.id) * 4.2) +
                    (SELECT COUNT(*) FROM user_behavior ub WHERE ub.target_type='post' AND ub.target_id = p.id AND ub.action_type='view') * 0.8 +
                    (CASE WHEN EXISTS(SELECT 1 FROM followers f WHERE f.follower_id = :uid AND f.following_id = p.user_id) THEN 200 ELSE 0 END) +
                    (CASE WHEN EXISTS(
                        SELECT 1 FROM post_tags pt
                        JOIN user_preferences up ON up.user_id = :uid
                        WHERE pt.post_id = p.id
                          AND up.preferred_categories IS NOT NULL
                          AND JSON_CONTAINS(up.preferred_categories, JSON_QUOTE(pt.tag))
                    ) THEN 150 ELSE 0 END)
                )
                / POW(GREATEST(1, TIMESTAMPDIFF(HOUR, p.created_at, NOW()) / 3), 1.18) DESC,
                p.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Guest trending feed
        $sql = "
            SELECT
                p.*,
                u.username,
                u.profile_picture,
                COALESCE(cs.total_score, 50) AS learned_score,
                (SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'like') AS likes,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments,
                (SELECT COUNT(*) FROM posts r WHERE r.original_post_id = p.id) AS reposts,
                (SELECT COUNT(*) FROM user_behavior ub WHERE ub.target_type = 'post' AND ub.target_id = p.id AND ub.action_type = 'view') AS views
            FROM posts p
            JOIN users u ON u.id = p.user_id
            LEFT JOIN content_scores cs ON cs.post_id = p.id
            WHERE p.original_post_id IS NULL
            ORDER BY
                (
                    ((SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type='like') * 3.2) +
                    ((SELECT COUNT(*) FROM comments WHERE post_id = p.id) * 4.0) +
                    ((SELECT COUNT(*) FROM posts r WHERE r.original_post_id = p.id) * 5.0) +
                    (SELECT COUNT(*) FROM user_behavior ub WHERE ub.target_type='post' AND ub.target_id = p.id AND ub.action_type='view') * 0.9 +
                    COALESCE(cs.total_score, 50) * 0.8
                )
                / POW(GREATEST(1, TIMESTAMPDIFF(HOUR, p.created_at, NOW()) / 2), 1.3) DESC,
                p.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Updater: recompute content_scores for recent posts.
 * Intended to be run from CLI (cron): php index.php update_scores
 */
function content_score_updater($pdo)
{
    // Look back window for updates (days)
    $lookback_days = 30;

    $sql = "SELECT id, created_at FROM posts WHERE created_at >= NOW() - INTERVAL :days DAY";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':days' => $lookback_days]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare count statements
    $cntLikesStmt = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE post_id = ? AND type = 'like'");
    $cntDislikesStmt = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE post_id = ? AND type = 'dislike'");
    $cntCommentsStmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
    $cntRepostsStmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE original_post_id = ?");
    $cntViewsStmt = $pdo->prepare("SELECT COUNT(*) FROM user_behavior WHERE target_type='post' AND target_id = ? AND action_type='view' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $recentViewsStmt = $pdo->prepare("SELECT COUNT(*) FROM user_behavior WHERE target_type='post' AND target_id = ? AND action_type='view' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 HOUR)");

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

        $recentViewsStmt->execute([$postId]);
        $recentViews = (int) $recentViewsStmt->fetchColumn();

        // Engagement score weights
        $engagement_score = ($likes * 2.5) + ($comments * 4.0) + ($reposts * 5.0) + ($views * 0.7);

        // Virality: percent recent activity + repost bump
        $virality = 0;
        if ($views > 0) {
            $virality = ($recentViews / max(1, $views)) * 100;
        }
        $virality += min(50, $reposts * 8);

        // Penalty for dislikes
        $penalty = ($dislikes * 2.0);

        // Age factor (hours old)
        $created = new DateTime($p['created_at']);
        $now = new DateTime();
        $hours_old = max(1, ($now->getTimestamp() - $created->getTimestamp()) / 3600);
        $age_factor = pow(max(1, $hours_old / 24), 0.7);

        // total score
        $total_score = (($engagement_score * 1.0) + ($virality * 2.0) - ($penalty)) / $age_factor;
        $total_score = round(max(0, min(9999, $total_score)), 2);

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

    echo "Updated content_scores for " . count($posts) . " posts.\n";
}

/**
 * Time formatting helper (kept from your original file)
 */
function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full)
        $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WetWater</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <!-- Floating Action Button -->
    <?php if ($is_logged_in): ?>
        <button class="floating-action-btn" onclick="openCreateModal()">
            <i class="bi bi-plus-lg"></i>
        </button>
    <?php else: ?>
        <a href="login.php" class="floating-action-btn"
            style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-plus-lg"></i>
        </a>
    <?php endif; ?>

    <div class="feed-container">
        <!-- Stories Carousel -->
        <div class="story-carousel">
            <div class="d-flex overflow-auto" style="scrollbar-width: none;">
                <?php
                // Fetch active stories (same as before)
                $stmt = $pdo->prepare("
                    SELECT s.*, u.username, u.profile_picture 
                    FROM stories s 
                    JOIN users u ON s.user_id = u.id 
                    WHERE s.expires_at > NOW() 
                    ORDER BY s.created_at DESC 
                    LIMIT 10
                ");
                $stmt->execute();
                $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($stories)) {
                    foreach ($stories as $story):
                        ?>
                        <div class="story-item" onclick="viewStory(<?php echo $story['id']; ?>)">
                            <img src="<?php echo htmlspecialchars($story['media_url']); ?>" class="story-avatar"
                                alt="<?php echo htmlspecialchars($story['username']); ?>">
                            <div class="story-username"><?php echo htmlspecialchars($story['username']); ?></div>
                        </div>
                        <?php
                    endforeach;
                } else {
                    echo '<div class="text-center text-muted w-100">No active stories</div>';
                }
                ?>
            </div>
        </div>

        <!-- Feed Items -->
        <div id="feed-items">
            <?php if (empty($posts)): ?>
                <div class="login-prompt">
                    <h4>Welcome to Spark! 🔥</h4>
                    <p>Connect with friends and discover amazing content.</p>
                    <?php if (!$is_logged_in): ?>
                        <a href="login.php" class="btn btn-primary">Login to Get Started</a>
                        <a href="register.php" class="btn btn-outline-light ms-2">Sign Up</a>
                    <?php else: ?>
                        <p class="text-muted">No posts yet. Be the first to share!</p>
                        <a href="create_post.php" class="btn btn-primary">Create Your First Post</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="feed-item" data-post-id="<?php echo $post['id']; ?>">
                        <!-- Post Header -->
                        <div class="post-header">
                            <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" class="user-avatar"
                                alt="<?php echo htmlspecialchars($post['username']); ?>"
                                onerror="this.src='default_profile.jpg'">
                            <div class="user-info">
                                <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="username">
                                    <?php echo htmlspecialchars($post['username']); ?>
                                    <?php if (!empty($post['learned_score']) && $post['learned_score'] > 80): ?>
                                        <span class="trending-badge">TRENDING</span>
                                    <?php endif; ?>
                                </a>
                                <div class="post-time">
                                    <?php echo time_elapsed_string($post['created_at']); ?>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-three-dots"></i>
                            </button>
                        </div>

                        <!-- Post Content -->
                        <div class="post-content">
                            <?php if (!empty($post['image'])): ?>
                                <img src="<?php echo htmlspecialchars($post['image']); ?>" class="post-image" alt="Post image"
                                    onclick="toggleImageSize(this)" onerror="this.style.display='none'">
                            <?php endif; ?>

                            <?php if (!empty($post['content'])): ?>
                                <div class="post-text">
                                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($post['caption'])): ?>
                                <div class="post-text text-muted">
                                    <small><?php echo nl2br(htmlspecialchars($post['caption'])); ?></small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Post Actions -->
                        <div class="post-actions">
                            <button class="action-btn like-btn <?php echo !empty($post['user_liked']) ? 'liked' : ''; ?>"
                                onclick="reactToPost(<?php echo $post['id']; ?>, 'like')" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                                <i
                                    class="bi bi-heart<?php echo !empty($post['user_liked']) ? '-fill' : ''; ?>"></i><?php echo (isset($post['likes']) ? $post['likes'] : 0); ?>
                            </button>

                            <button class="action-btn" onclick="toggleComments(<?php echo $post['id']; ?>)" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                                <i class="bi bi-chat"></i> <?php echo (isset($post['comments']) ? $post['comments'] : 0); ?>
                            </button>

                            <!-- Repost Button -->
                            <button class="action-btn repost-btn" onclick="repost(<?php echo $post['id']; ?>)" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                                <i class="bi bi-arrow-repeat"></i>
                                <span
                                    class="repost-count"><?php echo (isset($post['reposts']) ? $post['reposts'] : 0); ?></span>
                            </button>

                            <button class="action-btn" onclick="sharePost(<?php echo $post['id']; ?>)" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                                <i class="bi bi-share"></i>
                            </button>
                        </div>

                        <!-- Comments Section (Initially Hidden) -->
                        <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display: none;">
                            <div class="comments-container" id="comments-container-<?php echo $post['id']; ?>">
                                <!-- Comments will be loaded here via AJAX -->
                            </div>

                            <!-- Add Comment Form -->
                            <?php if ($is_logged_in): ?>
                                <div class="add-comment-form-container">
                                    <form class="add-comment-form" data-post-id="<?php echo $post['id']; ?>">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Write a comment..." required>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Infinite Scroll Loader -->
        <div id="infinite-scroll-loader" class="infinite-scroll-loader" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- JavaScript (kept mostly identical; AJAX endpoint updated to index.php?action=load_posts ) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = <?php echo $page; ?>;
        let isLoading = false;
        let hasMore = true;

        // Infinite Scroll
        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                loadMorePosts();
            }
        });

        function loadMorePosts() {
            if (isLoading || !hasMore) return;

            isLoading = true;
            currentPage++;

            $('#infinite-scroll-loader').show();

            $.ajax({
                url: 'index.php?action=load_posts',
                method: 'GET',
                data: { page: currentPage },
                dataType: 'json',
                success: function (response) {
                    if (response.posts && response.posts.length > 0) {
                        $('#feed-items').append(response.html);
                        hasMore = response.hasMore;
                        initializeCommentEvents();
                    } else {
                        hasMore = false;
                        $('#infinite-scroll-loader').html('<p class="text-muted">No more posts to load</p>');
                    }
                },
                error: function () {
                    hasMore = false;
                    $('#infinite-scroll-loader').html('<p class="text-muted">Error loading posts</p>');
                },
                complete: function () {
                    isLoading = false;
                    $('#infinite-scroll-loader').hide();
                }
            });
        }

        // React to Post
        function reactToPost(postId, type) {
            <?php if (!$is_logged_in): ?>
                window.location.href = 'login.php';
                return;
            <?php endif; ?>

            const btn = $(`.like-btn[onclick*="reactToPost(${postId}, '${type}')"]`);

            $.post('react.php', {
                post_id: postId,
                type: type,
                ajax: true
            }, function (response) {
                if (response.success) {
                    if (type === 'like') {
                        if (response.action === 'added' || response.action === 'updated') {
                            btn.addClass('liked').find('i').removeClass('bi-heart').addClass('bi-heart-fill');
                            const newCount = response.likes;
                            // Replace only number portion (safe approach)
                            btn.html(btn.find('i')[0].outerHTML + newCount);
                            btn.find('i').css('animation', 'heartBeat 0.6s ease');
                            setTimeout(() => { btn.find('i').css('animation', ''); }, 600);
                        } else {
                            btn.removeClass('liked').find('i').removeClass('bi-heart-fill').addClass('bi-heart');
                            const newCount = response.likes;
                            btn.html(btn.find('i')[0].outerHTML + newCount);
                        }
                    }
                    showToast('Post ' + type + 'd successfully!', 'success');
                } else {
                    showToast(response.message || 'Error reacting to post', 'error');
                }
            }, 'json').fail(function (xhr, status, error) {
                console.error('Error:', error);
                showToast('Error reacting to post. Please try again.', 'error');
            });
        }

        // Repost
        function repost(postId) {
            <?php if (!$is_logged_in): ?>
                window.location.href = 'login.php';
                return;
            <?php endif; ?>

            const btn = $(`.repost-btn[onclick*="repost(${postId})"]`);

            $.post('repost.php', { post_id: postId }, function (response) {
                if (response.success) {
                    const repostCount = btn.find('.repost-count');
                    const currentCount = parseInt(repostCount.text()) || 0;

                    if (response.action === 'added') {
                        repostCount.text(currentCount + 1);
                        showToast('Post reposted successfully!', 'success');
                        btn.addClass('active');
                    } else {
                        repostCount.text(Math.max(0, currentCount - 1));
                        showToast('Repost removed!', 'info');
                        btn.removeClass('active');
                    }
                } else {
                    showToast(response.message || 'Error reposting', 'error');
                }
            }, 'json').fail(function (xhr, status, error) {
                console.error('Error:', error);
                showToast('Error reposting. Please try again.', 'error');
            });
        }

        // Toggle Comments
        function toggleComments(postId) {
            <?php if (!$is_logged_in): ?>
                window.location.href = 'login.php';
                return;
            <?php endif; ?>

            const commentsSection = $('#comments-' + postId);
            const commentsContainer = $('#comments-container-' + postId);

            if (commentsSection.is(':visible')) {
                commentsSection.slideUp(300);
            } else {
                commentsSection.slideDown(300);
                if (commentsContainer.html().trim() === '') {
                    loadComments(postId);
                }
            }
        }

        function loadComments(postId) {
            const commentsContainer = $('#comments-container-' + postId);
            commentsContainer.html('<div class="text-center p-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading comments...</p></div>');

            $.get('fetch_comments.php', { post_id: postId }, function (html) {
                commentsContainer.html(html);
                initializeCommentEvents();
            }).fail(function (xhr, status, error) {
                console.error('Error loading comments:', error);
                commentsContainer.html('<p class="text-danger p-3">Error loading comments. Please try again.</p>');
            });
        }

        // Initialize comment events (same as your original)
        function initializeCommentEvents() {
            // Unbind then bind to avoid duplicates
            $(document).off('submit', '.add-comment-form').on('submit', '.add-comment-form', function (e) {
                e.preventDefault();
                const form = $(this);
                const postId = form.data('post-id');
                const content = form.find('input[type="text"]').val().trim();
                if (!content) { showToast('Please enter a comment', 'error'); return; }

                $.post('add_comment.php', { post_id: postId, content: content }, function (response) {
                    if (response.success) {
                        form.find('input[type="text"]').val('');
                        loadComments(postId);
                        showToast('Comment added successfully!', 'success');

                        // Update comment count
                        const commentBtn = $(`.action-btn[onclick*="toggleComments(${postId})"]`);
                        const currentText = commentBtn.text();
                        const currentCount = parseInt(currentText.match(/\d+/)?.[0] || 0);
                        commentBtn.html('<i class="bi bi-chat"></i> ' + (currentCount + 1));
                    } else {
                        showToast(response.message || 'Error adding comment', 'error');
                    }
                }, 'json').fail(function () {
                    showToast('Error adding comment. Please try again.', 'error');
                });
            });

            // Reply events
            $(document).off('submit', '.reply-comment-form').on('submit', '.reply-comment-form', function (e) {
                e.preventDefault();
                const form = $(this);
                const commentId = form.data('comment-id');
                const content = form.find('input[type="text"]').val().trim();
                if (!content) { showToast('Please enter a reply', 'error'); return; }

                $.post('add_comment.php', { parent_comment_id: commentId, content: content }, function (response) {
                    if (response.success) {
                        form.find('input[type="text"]').val('');
                        form.closest('.reply-form').hide();
                        const postId = form.closest('.feed-item').data('post-id');
                        loadComments(postId);
                        showToast('Reply added successfully!', 'success');
                    } else {
                        showToast(response.message || 'Error adding reply', 'error');
                    }
                }, 'json').fail(function () {
                    showToast('Error adding reply. Please try again.', 'error');
                });
            });

            $(document).off('click', '.reply-btn').on('click', '.reply-btn', function () {
                const commentId = $(this).data('comment-id');
                const replyForm = $('#reply-form-' + commentId);
                replyForm.slideToggle(200);
            });

            $(document).off('click', '.toggle-replies').on('click', '.toggle-replies', function () {
                const commentId = $(this).data('comment-id');
                const repliesContainer = $('#replies-' + commentId);
                const icon = $(this).find('i');
                repliesContainer.slideToggle(200, function () {
                    if (repliesContainer.is(':visible')) {
                        icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
                    } else {
                        icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
                    }
                });
            });
        }

        // Share Post
        function sharePost(postId) {
            <?php if (!$is_logged_in): ?>
                window.location.href = 'login.php';
                return;
            <?php endif; ?>

            const shareUrl = window.location.origin + '/share.php?post_id=' + postId;

            if (navigator.share) {
                navigator.share({
                    title: 'Check this post on Spark!',
                    text: 'I found this amazing post on Spark',
                    url: shareUrl
                }).then(() => {
                    showToast('Post shared successfully!', 'success');
                    updateShareCount(postId);
                }).catch(err => {
                    copyToClipboard(shareUrl, postId);
                });
            } else {
                copyToClipboard(shareUrl, postId);
            }
        }

        function copyToClipboard(text, postId) {
            navigator.clipboard.writeText(text).then(function () {
                showToast('Post link copied to clipboard!', 'success');
                updateShareCount(postId);
            }, function (err) {
                alert('Share this link: ' + text);
                updateShareCount(postId);
            });
        }

        function updateShareCount(postId) {
            $.post('update_share_count.php', { post_id: postId }).fail(function () {
                console.log('Failed to track share');
            });
        }

        // Toast helper
        function showToast(message, type = 'success') {
            $('.toast').remove();
            const toast = $(`
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`);
            $('body').append(toast);
            const toastInstance = new bootstrap.Toast(toast[0]);
            toastInstance.show();
            toast.on('hidden.bs.toast', function () { $(this).remove(); });
        }

        // Toggle Image Size
        function toggleImageSize(img) {
            $(img).toggleClass('engagement-pulse');
            if (img.style.maxHeight === 'none') {
                img.style.maxHeight = '600px';
                img.style.width = '100%';
            } else {
                img.style.maxHeight = 'none';
                img.style.width = 'auto';
            }
        }

        // View Story
        function viewStory(storyId) {
            window.location.href = 'story.php?id=' + storyId;
        }

        // Open Create Modal
        function openCreateModal() {
            window.location.href = 'create_post.php';
        }

        // Initialize comment events on page load
        $(document).ready(function () {
            initializeCommentEvents();

            // Re-initialize when new content is loaded via AJAX
            $(document).ajaxComplete(function () {
                initializeCommentEvents();
            });
        });

        // Swipe functionality for mobile
        let startY;
        document.addEventListener('touchstart', e => { startY = e.touches[0].clientY; });
        document.addEventListener('touchend', e => {
            if (!startY) return;
            const endY = e.changedTouches[0].clientY;
            const diff = startY - endY;
            if (diff > 50) { loadMorePosts(); }
            startY = null;
        });
    </script>
</body>

</html>

<?php include 'includes/footer.php'; ?>