<?php
session_start();
include 'config/db.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Get page number from AJAX request
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Function to get posts (same as index.php)
function getPersonalizedFeed($pdo, $user_id = null, $page = 1, $limit = 10)
{
    $offset = ($page - 1) * $limit;
    $limit = (int) $limit;
    $offset = (int) $offset;

    if ($user_id) {
        // For logged-in users: Personalized feed with advanced algorithm
        $query = "
            SELECT 
                p.*,
                u.username,
                u.profile_picture,
                (SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'like') AS likes,
                (SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'dislike') AS dislikes,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments,
                (SELECT COUNT(*) FROM posts AS reposts WHERE reposts.original_post_id = p.id) AS reposts,
                COALESCE(cs.total_score, 50) as content_score,
                EXISTS(SELECT 1 FROM reactions WHERE post_id = p.id AND user_id = ? AND type = 'like') as user_liked,
                EXISTS(SELECT 1 FROM reactions WHERE post_id = p.id AND user_id = ? AND type = 'dislike') as user_disliked
            FROM posts p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN content_scores cs ON cs.post_id = p.id
            WHERE p.original_post_id IS NULL
            ORDER BY 
                CASE 
                    WHEN EXISTS(SELECT 1 FROM followers WHERE follower_id = ? AND following_id = p.user_id) THEN 1000
                    ELSE COALESCE(cs.total_score, 50) 
                END DESC,
                ((SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'like') * 2 + 
                 (SELECT COUNT(*) FROM comments WHERE post_id = p.id) * 3 + 
                 (SELECT COUNT(*) FROM posts AS reposts WHERE reposts.original_post_id = p.id) * 4) DESC,
                p.created_at DESC
            LIMIT ?
            OFFSET ?
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(4, $limit, PDO::PARAM_INT);
        $stmt->bindValue(5, $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // For non-logged-in users: Trending content
        $query = "
            SELECT 
                p.*,
                u.username,
                u.profile_picture,
                (SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'like') AS likes,
                (SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'dislike') AS dislikes,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments,
                (SELECT COUNT(*) FROM posts AS reposts WHERE reposts.original_post_id = p.id) AS reposts,
                COALESCE(cs.total_score, 50) as content_score,
                0 as user_liked,
                0 as user_disliked
            FROM posts p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN content_scores cs ON cs.post_id = p.id
            WHERE p.original_post_id IS NULL
            ORDER BY 
                ((SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'like') * 2 + 
                 (SELECT COUNT(*) FROM comments WHERE post_id = p.id) * 3 + 
                 (SELECT COUNT(*) FROM posts AS reposts WHERE reposts.original_post_id = p.id) * 4) DESC,
                COALESCE(cs.total_score, 50) DESC,
                p.created_at DESC
            LIMIT ?
            OFFSET ?
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Helper function to format time (same as index.php)
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

// Get posts for the current page
$posts = getPersonalizedFeed($pdo, $user_id, $page, $limit);

// Track views for recommendation algorithm
if ($user_id && !empty($posts)) {
    foreach ($posts as $post) {
        $stmt = $pdo->prepare("INSERT INTO user_behavior (user_id, action_type, target_id, target_type) VALUES (?, 'view', ?, 'post')");
        $stmt->execute([$user_id, $post['id']]);
    }
}

// Generate HTML for the posts
$html = '';
if (empty($posts)) {
    $response = [
        'success' => true,
        'posts' => [],
        'html' => '',
        'hasMore' => false
    ];
} else {
    foreach ($posts as $post) {
        $html .= '
        <div class="feed-item" data-post-id="' . $post['id'] . '">
            <!-- Post Header -->
            <div class="post-header">
                <img src="' . htmlspecialchars($post['profile_picture']) . '" class="user-avatar"
                    alt="' . htmlspecialchars($post['username']) . '"
                    onerror="this.src=\'default_profile.jpg\'">
                <div class="user-info">
                    <a href="profile.php?id=' . $post['user_id'] . '" class="username">
                        ' . htmlspecialchars($post['username']) . '
                        ' . ($post['content_score'] > 80 ? '<span class="trending-badge">TRENDING</span>' : '') . '
                    </a>
                    <div class="post-time">
                        ' . time_elapsed_string($post['created_at']) . '
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-three-dots"></i>
                </button>
            </div>

            <!-- Post Content -->
            <div class="post-content">
                ' . (!empty($post['image']) ? '<img src="' . htmlspecialchars($post['image']) . '" class="post-image" alt="Post image" onclick="toggleImageSize(this)" onerror="this.style.display=\'none\'">' : '') . '

                ' . (!empty($post['content']) ? '<div class="post-text">' . nl2br(htmlspecialchars($post['content'])) . '</div>' : '') . '

                ' . (!empty($post['caption']) ? '<div class="post-text text-muted"><small>' . nl2br(htmlspecialchars($post['caption'])) . '</small></div>' : '') . '
            </div>

            <!-- Post Actions -->
            <div class="post-actions">
                <button class="action-btn like-btn ' . ($post['user_liked'] ? 'liked' : '') . '"
                    onclick="reactToPost(' . $post['id'] . ', \'like\')" ' . (!$is_logged_in ? 'disabled' : '') . '>
                    <i class="bi bi-heart' . ($post['user_liked'] ? '-fill' : '') . '"></i>' . $post['likes'] . '
                </button>

                <button class="action-btn" onclick="toggleComments(' . $post['id'] . ')" ' . (!$is_logged_in ? 'disabled' : '') . '>
                    <i class="bi bi-chat"></i> ' . $post['comments'] . '
                </button>

                <!-- Repost Button -->
                <button class="action-btn repost-btn" onclick="repost(' . $post['id'] . ')" ' . (!$is_logged_in ? 'disabled' : '') . '>
                    <i class="bi bi-arrow-repeat"></i>
                    <span class="repost-count">' . $post['reposts'] . '</span>
                </button>

                <button class="action-btn" onclick="sharePost(' . $post['id'] . ')" ' . (!$is_logged_in ? 'disabled' : '') . '>
                    <i class="bi bi-share"></i>
                </button>
            </div>

            <!-- Comments Section (Initially Hidden) -->
            <div class="comments-section" id="comments-' . $post['id'] . '" style="display: none;">
                <div class="comments-container" id="comments-container-' . $post['id'] . '">
                    <!-- Comments will be loaded here via AJAX -->
                </div>
                
                <!-- Add Comment Form -->
                ' . ($is_logged_in ? '
                <div class="add-comment-form-container">
                    <form class="add-comment-form" data-post-id="' . $post['id'] . '">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Write a comment..." required>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </form>
                </div>' : '') . '
            </div>
        </div>';
    }

    $response = [
        'success' => true,
        'posts' => $posts,
        'html' => $html,
        'hasMore' => count($posts) === $limit
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>