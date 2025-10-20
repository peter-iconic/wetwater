<?php
// load_posts.php
session_start();
include 'config/db.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;

// Include the same feed function (copy the function or include it from a shared file)
function getPersonalizedFeed($pdo, $user_id = null, $page = 1, $limit = 10)
{
    // (You can copy the exact function body from index.php here, or 'require' a shared file.)
    // For brevity, require the function from a shared include if you made one:
    require_once 'feed_functions.php'; // OPTIONAL: if you put the function in feed_functions.php
}

// If you didn't extract the function, simply include the function code directly here.
// For now, assuming index.php and this file share the same function code via include:
if (!function_exists('getPersonalizedFeed')) {
    // copy the function body here OR include it
    require_once 'index.php'; // WARNING: only if index.php safely defines the function without output duplication
}

// Get posts
$posts = getPersonalizedFeed($pdo, $user_id, $page, $limit);

// Track view for recommendation algorithm (one insert per visible post)
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

// Build HTML fragment for returned posts (same structure as index.php)
$html = '';
foreach ($posts as $post) {
    ob_start();
    ?>
    <div class="feed-item" data-post-id="<?php echo $post['id']; ?>">
        <div class="post-header">
            <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" class="user-avatar"
                alt="<?php echo htmlspecialchars($post['username']); ?>" onerror="this.src='default_profile.jpg'">
            <div class="user-info">
                <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="username">
                    <?php echo htmlspecialchars($post['username']); ?>
                    <?php if ($post['learned_score'] > 80): ?>
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
                onclick="reactToPost(<?php echo $post['id']; ?>, 'like')" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                <i
                    class="bi bi-heart<?php echo !empty($post['user_liked']) ? '-fill' : ''; ?>"></i><?php echo $post['likes']; ?>
            </button>
            <button class="action-btn" onclick="toggleComments(<?php echo $post['id']; ?>)" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                <i class="bi bi-chat"></i> <?php echo $post['comments']; ?>
            </button>
            <button class="action-btn repost-btn" onclick="repost(<?php echo $post['id']; ?>)" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                <i class="bi bi-arrow-repeat"></i> <span class="repost-count"><?php echo $post['reposts']; ?></span>
            </button>
            <button class="action-btn" onclick="sharePost(<?php echo $post['id']; ?>)" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                <i class="bi bi-share"></i>
            </button>
        </div>

        <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display: none;">
            <div class="comments-container" id="comments-container-<?php echo $post['id']; ?>"></div>
            <?php if ($is_logged_in): ?>
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
    <?php
    $html .= ob_get_clean();
}

$response = [
    'posts' => $posts,
    'html' => $html,
    'hasMore' => count($posts) >= $limit
];

header('Content-Type: application/json');
echo json_encode($response);
