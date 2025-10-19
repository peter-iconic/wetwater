<?php
session_start();
include 'config/db.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Use the same function from index.php
function getPersonalizedFeed($pdo, $user_id = null, $page = 1, $limit = 10)
{
    $offset = ($page - 1) * $limit;
    $limit = (int) $limit;
    $offset = (int) $offset;

    if ($user_id) {
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

$posts = getPersonalizedFeed($pdo, $user_id, $page, 10);

// Generate HTML for new posts
$html = '';
foreach ($posts as $post) {
    $html .= '
    <div class="feed-item" data-post-id="' . $post['id'] . '">
        <div class="post-header">
            <img src="' . htmlspecialchars($post['profile_picture']) . '" 
                 class="user-avatar" 
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
        <div class="post-content">
            ' . ($post['image'] ? '<img src="' . htmlspecialchars($post['image']) . '" class="post-image" alt="Post image" onclick="toggleImageSize(this)" onerror="this.style.display=\'none\'">' : '') . '
            ' . ($post['content'] ? '<div class="post-text">' . nl2br(htmlspecialchars($post['content'])) . '</div>' : '') . '
            ' . ($post['caption'] ? '<div class="post-text text-muted"><small>' . nl2br(htmlspecialchars($post['caption'])) . '</small></div>' : '') . '
        </div>
        <div class="stats">
            <span class="me-3"><i class="bi bi-heart-fill text-danger"></i> ' . $post['likes'] . '</span>
            <span class="me-3"><i class="bi bi-chat"></i> ' . $post['comments'] . '</span>
            <span><i class="bi bi-share"></i> ' . $post['reposts'] . '</span>
        </div>
        <div class="post-actions">
            <button class="action-btn like-btn ' . ($post['user_liked'] ? 'liked' : '') . '" 
                    onclick="reactToPost(' . $post['id'] . ', \'like\')">
                <i class="bi bi-heart' . ($post['user_liked'] ? '-fill' : '') . '"></i>
                <span>Like</span>
            </button>
            <button class="action-btn" onclick="toggleComments(' . $post['id'] . ')">
                <i class="bi bi-chat"></i>
                <span>Comment</span>
            </button>
            <button class="action-btn" onclick="sharePost(' . $post['id'] . ')">
                <i class="bi bi-share"></i>
                <span>Share</span>
            </button>
            <button class="action-btn" onclick="savePost(' . $post['id'] . ')">
                <i class="bi bi-bookmark"></i>
                <span>Save</span>
            </button>
        </div>
        <div class="comments-section" id="comments-' . $post['id'] . '" style="display: none;"></div>
    </div>';
}

header('Content-Type: application/json');
echo json_encode([
    'html' => $html,
    'hasMore' => count($posts) === 10,
    'posts' => $posts
]);

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