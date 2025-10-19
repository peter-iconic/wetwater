<?php
session_start();
include 'config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$post_id = intval($_GET['id']);
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Fetch the specific post
try {
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            u.username,
            u.profile_picture,
            (SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'like') AS likes,
            (SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'dislike') AS dislikes,
            (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments,
            (SELECT COUNT(*) FROM posts AS reposts WHERE reposts.original_post_id = p.id) AS reposts,
            EXISTS(SELECT 1 FROM reactions WHERE post_id = p.id AND user_id = ? AND type = 'like') as user_liked,
            EXISTS(SELECT 1 FROM reactions WHERE post_id = p.id AND user_id = ? AND type = 'dislike') as user_disliked
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = ?
    ");
    
    if ($is_logged_in) {
        $stmt->execute([$user_id, $user_id, $post_id]);
    } else {
        // For non-logged-in users, we still need to execute with null values
        $stmt = $pdo->prepare("
            SELECT 
                p.*,
                u.username,
                u.profile_picture,
                (SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'like') AS likes,
                (SELECT COUNT(*) FROM reactions WHERE post_id = p.id AND type = 'dislike') AS dislikes,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments,
                (SELECT COUNT(*) FROM posts AS reposts WHERE reposts.original_post_id = p.id) AS reposts,
                0 as user_liked,
                0 as user_disliked
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$post_id]);
    }
    
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        header("Location: index.php");
        exit();
    }
    
    // Track view for analytics
    if ($is_logged_in) {
        $stmt = $pdo->prepare("INSERT INTO user_behavior (user_id, action_type, target_id, target_type) VALUES (?, 'view', ?, 'post')");
        $stmt->execute([$user_id, $post_id]);
    }
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle reactions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_logged_in) {
    if (isset($_POST['react'])) {
        $type = $_POST['type'];
        
        // Check if user already reacted
        $stmt = $pdo->prepare("SELECT id, type FROM reactions WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
        $existing_reaction = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing_reaction) {
            if ($existing_reaction['type'] === $type) {
                // Remove reaction
                $stmt = $pdo->prepare("DELETE FROM reactions WHERE id = ?");
                $stmt->execute([$existing_reaction['id']]);
            } else {
                // Update reaction
                $stmt = $pdo->prepare("UPDATE reactions SET type = ? WHERE id = ?");
                $stmt->execute([$type, $existing_reaction['id']]);
            }
        } else {
            // Insert new reaction
            $stmt = $pdo->prepare("INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $type]);
        }
        
        header("Location: post.php?id=" . $post_id);
        exit();
    }
    
    // Handle comments
    if (isset($_POST['comment'])) {
        $content = trim($_POST['content']);
        if (!empty($content)) {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $content]);
            
            header("Location: post.php?id=" . $post_id);
            exit();
        }
    }
    
    // Handle replies
    if (isset($_POST['reply'])) {
        $content = trim($_POST['content']);
        $parent_comment_id = intval($_POST['parent_comment_id']);
        if (!empty($content)) {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, parent_comment_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $content, $parent_comment_id]);
            
            header("Location: post.php?id=" . $post_id);
            exit();
        }
    }
}

// Fetch comments for this post
$stmt = $pdo->prepare("
    SELECT c.*, u.username, u.profile_picture 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = ? AND c.parent_comment_id IS NULL 
    ORDER BY c.created_at ASC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

function time_elapsed_string($datetime, $full = false) {
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

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['username']); ?>'s Post - Spark</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #6f42c1;
            --secondary-color: #e83e8c;
            --dark-bg: #0f0f0f;
            --card-bg: #1a1a1a;
            --text-light: #ffffff;
            --text-muted: #aaaaaa;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .post-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .post-card {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }

        .post-header {
            padding: 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #333;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .user-info {
            flex: 1;
        }

        .username {
            font-weight: 600;
            color: var(--text-light);
            text-decoration: none;
            font-size: 1.1rem;
        }

        .post-time {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .post-content {
            padding: 0;
        }

        .post-image {
            width: 100%;
            max-height: 600px;
            object-fit: cover;
        }

        .post-text {
            padding: 20px;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .post-stats {
            padding: 15px 20px;
            border-bottom: 1px solid #333;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .post-actions {
            padding: 15px 20px;
            display: flex;
            justify-content: space-around;
            border-bottom: 1px solid #333;
        }

        .action-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 1rem;
        }

        .action-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
        }

        .action-btn.liked {
            color: #e74c3c;
        }

        .comments-section {
            padding: 20px;
        }

        .comment-item {
            padding: 15px 0;
            border-bottom: 1px solid #333;
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .comment-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .comment-username {
            font-weight: 600;
            color: var(--text-light);
            text-decoration: none;
        }

        .comment-time {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-left: 10px;
        }

        .comment-content {
            margin-left: 45px;
            line-height: 1.5;
        }

        .replies {
            margin-left: 45px;
            margin-top: 10px;
            padding-left: 15px;
            border-left: 2px solid #333;
        }

        .reply-form {
            margin-top: 10px;
            display: none;
        }

        .back-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #5a32a3;
            color: white;
        }
    </style>
</head>
<body>
    <div class="post-container">
        <!-- Back Button -->
        <a href="index.php" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back to Feed
        </a>

        <!-- Post Card -->
        <div class="post-card">
            <!-- Post Header -->
            <div class="post-header">
                <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" 
                     class="user-avatar" 
                     alt="<?php echo htmlspecialchars($post['username']); ?>"
                     onerror="this.src='default_profile.jpg'">
                <div class="user-info">
                    <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="username">
                        <?php echo htmlspecialchars($post['username']); ?>
                    </a>
                    <div class="post-time">
                        <?php echo time_elapsed_string($post['created_at']); ?>
                    </div>
                </div>
            </div>

            <!-- Post Content -->
            <div class="post-content">
                <?php if(!empty($post['image'])): ?>
                <img src="<?php echo htmlspecialchars($post['image']); ?>" 
                     class="post-image" 
                     alt="Post image"
                     onerror="this.style.display='none'">
                <?php endif; ?>
                
                <?php if(!empty($post['content'])): ?>
                <div class="post-text">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>
                <?php endif; ?>

                <?php if(!empty($post['caption'])): ?>
                <div class="post-text text-muted">
                    <small><?php echo nl2br(htmlspecialchars($post['caption'])); ?></small>
                </div>
                <?php endif; ?>
            </div>

            <!-- Post Stats -->
            <div class="post-stats">
                <span class="me-3">
                    <i class="bi bi-heart-fill text-danger"></i> <?php echo $post['likes']; ?> likes
                </span>
                <span class="me-3">
                    <i class="bi bi-chat"></i> <?php echo $post['comments']; ?> comments
                </span>
                <span>
                    <i class="bi bi-share"></i> <?php echo $post['reposts']; ?> shares
                </span>
            </div>

            <!-- Post Actions -->
            <div class="post-actions">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="react" value="1">
                    <input type="hidden" name="type" value="like">
                    <button type="submit" class="action-btn <?php echo $post['user_liked'] ? 'liked' : ''; ?>"
                            <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                        <i class="bi bi-heart<?php echo $post['user_liked'] ? '-fill' : ''; ?>"></i>
                        <span>Like</span>
                    </button>
                </form>
                
                <button class="action-btn" onclick="document.getElementById('comment-form').scrollIntoView({behavior: 'smooth'})"
                        <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                    <i class="bi bi-chat"></i>
                    <span>Comment</span>
                </button>
                
                <button class="action-btn" onclick="sharePost()"
                        <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                    <i class="bi bi-share"></i>
                    <span>Share</span>
                </button>
            </div>

            <!-- Comments Section -->
            <div class="comments-section">
                <h5>Comments (<?php echo $post['comments']; ?>)</h5>
                
                <?php if(empty($comments)): ?>
                    <p class="text-muted">No comments yet. Be the first to comment!</p>
                <?php else: ?>
                    <?php foreach($comments as $comment): ?>
                    <div class="comment-item">
                        <div class="comment-header">
                            <img src="<?php echo htmlspecialchars($comment['profile_picture']); ?>" 
                                 class="comment-avatar" 
                                 alt="<?php echo htmlspecialchars($comment['username']); ?>"
                                 onerror="this.src='default_profile.jpg'">
                            <a href="profile.php?id=<?php echo $comment['user_id']; ?>" class="comment-username">
                                <?php echo htmlspecialchars($comment['username']); ?>
                            </a>
                            <span class="comment-time"><?php echo time_elapsed_string($comment['created_at']); ?></span>
                        </div>
                        <div class="comment-content">
                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                            
                            <!-- Reply button -->
                            <?php if($is_logged_in): ?>
                            <button class="btn btn-sm btn-outline-secondary mt-2" onclick="toggleReplyForm(<?php echo $comment['id']; ?>)">
                                <i class="bi bi-reply"></i> Reply
                            </button>
                            <?php endif; ?>
                            
                            <!-- Reply form -->
                            <?php if($is_logged_in): ?>
                            <div class="reply-form" id="reply-form-<?php echo $comment['id']; ?>">
                                <form method="POST" class="mt-2">
                                    <input type="hidden" name="reply" value="1">
                                    <input type="hidden" name="parent_comment_id" value="<?php echo $comment['id']; ?>">
                                    <div class="input-group">
                                        <input type="text" name="content" class="form-control" placeholder="Write a reply..." required>
                                        <button type="submit" class="btn btn-primary">Reply</button>
                                    </div>
                                </form>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Display replies -->
                            <?php
                            $stmt = $pdo->prepare("
                                SELECT c.*, u.username, u.profile_picture 
                                FROM comments c 
                                JOIN users u ON c.user_id = u.id 
                                WHERE c.parent_comment_id = ? 
                                ORDER BY c.created_at ASC
                            ");
                            $stmt->execute([$comment['id']]);
                            $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (!empty($replies)): ?>
                            <div class="replies">
                                <?php foreach($replies as $reply): ?>
                                <div class="comment-item">
                                    <div class="comment-header">
                                        <img src="<?php echo htmlspecialchars($reply['profile_picture']); ?>" 
                                             class="comment-avatar" 
                                             alt="<?php echo htmlspecialchars($reply['username']); ?>"
                                             onerror="this.src='default_profile.jpg'">
                                        <a href="profile.php?id=<?php echo $reply['user_id']; ?>" class="comment-username">
                                            <?php echo htmlspecialchars($reply['username']); ?>
                                        </a>
                                        <span class="comment-time"><?php echo time_elapsed_string($reply['created_at']); ?></span>
                                    </div>
                                    <div class="comment-content">
                                        <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Comment Form -->
                <?php if($is_logged_in): ?>
                <div id="comment-form">
                    <form method="POST" class="mt-4">
                        <input type="hidden" name="comment" value="1">
                        <div class="input-group">
                            <input type="text" name="content" class="form-control" placeholder="Write a comment..." required>
                            <button type="submit" class="btn btn-primary">Comment</button>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <div class="text-center mt-4">
                    <a href="login.php" class="btn btn-primary">Login to Comment</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleReplyForm(commentId) {
            const form = document.getElementById('reply-form-' + commentId);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function sharePost() {
            const currentUrl = window.location.href;
            
            if (navigator.share) {
                navigator.share({
                    title: 'Check this post on Spark!',
                    url: currentUrl
                });
            } else {
                // Fallback to clipboard
                navigator.clipboard.writeText(currentUrl).then(function() {
                    alert('Post link copied to clipboard!');
                });
            }
        }
    </script>
</body>
</html>