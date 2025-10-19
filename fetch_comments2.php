<?php
session_start();
include 'config/db.php';

if (!isset($_GET['post_id'])) {
    die('Post ID required');
}

$post_id = intval($_GET['post_id']);
$is_logged_in = isset($_SESSION['user_id']);

try {
    // Fetch top-level comments
    $stmt = $pdo->prepare("
        SELECT c.*, u.username, u.profile_picture 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.post_id = ? AND c.parent_comment_id IS NULL 
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$post_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($comments)) {
        echo '<p class="text-muted p-3 text-center">No comments yet. Be the first to comment!</p>';
    } else {
        foreach ($comments as $comment) {
            echo '
            <div class="comment-item mb-3 p-3 border-bottom">
                <div class="d-flex">
                    <img src="' . htmlspecialchars($comment['profile_picture']) . '" 
                         class="rounded-circle me-2" 
                         width="32" height="32" 
                         alt="' . htmlspecialchars($comment['username']) . '"
                         onerror="this.src=\'default_profile.jpg\'">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>' . htmlspecialchars($comment['username']) . '</strong>
                                <small class="text-muted ms-2">' . time_elapsed_string($comment['created_at']) . '</small>
                            </div>
                        </div>
                        <p class="mb-1 mt-1">' . nl2br(htmlspecialchars($comment['content'])) . '</p>
                        
                        <!-- Reply button -->
                        ' . ($is_logged_in ? '
                        <button class="btn btn-sm btn-outline-secondary mt-1" onclick="showReplyForm(' . $comment['id'] . ')">
                            <i class="bi bi-reply"></i> Reply
                        </button>
                        ' : '') . '
                        
                        <!-- Reply form (hidden by default) -->
                        ' . ($is_logged_in ? '
                        <div class="reply-form mt-2" id="reply-form-' . $comment['id'] . '" style="display: none;">
                            <form class="reply-comment-form" data-comment-id="' . $comment['id'] . '">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Write a reply..." required>
                                    <button type="submit" class="btn btn-primary">Reply</button>
                                </div>
                            </form>
                        </div>
                        ' : '') . '
                        
                        <!-- Fetch and display replies -->
                        ' . getReplies($pdo, $comment['id'], $is_logged_in) . '
                    </div>
                </div>
            </div>';
        }
    }

    // Add comment form if user is logged in
    if ($is_logged_in) {
        echo '
        <div class="comment-form p-3">
            <form class="add-comment-form" data-post-id="' . $post_id . '">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Write a comment..." required>
                    <button type="submit" class="btn btn-primary">Comment</button>
                </div>
            </form>
        </div>';
    } else {
        echo '
        <div class="p-3 text-center">
            <a href="login.php" class="btn btn-primary">Login to Comment</a>
        </div>';
    }

} catch (PDOException $e) {
    echo '<p class="text-danger p-3">Error loading comments: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

function getReplies($pdo, $parent_comment_id, $is_logged_in)
{
    $stmt = $pdo->prepare("
        SELECT c.*, u.username, u.profile_picture 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.parent_comment_id = ? 
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$parent_comment_id]);
    $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($replies)) {
        return '';
    }

    $html = '<div class="replies ms-4 mt-2">';
    foreach ($replies as $reply) {
        $html .= '
        <div class="reply-item mb-2 p-2 bg-light rounded">
            <div class="d-flex">
                <img src="' . htmlspecialchars($reply['profile_picture']) . '" 
                     class="rounded-circle me-2" 
                     width="24" height="24" 
                     alt="' . htmlspecialchars($reply['username']) . '"
                     onerror="this.src=\'default_profile.jpg\'">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong class="small">' . htmlspecialchars($reply['username']) . '</strong>
                            <small class="text-muted ms-2">' . time_elapsed_string($reply['created_at']) . '</small>
                        </div>
                    </div>
                    <p class="mb-0 small">' . nl2br(htmlspecialchars($reply['content'])) . '</p>
                </div>
            </div>
        </div>';
    }
    $html .= '</div>';

    return $html;
}

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