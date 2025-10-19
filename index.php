<?php
session_start();
include 'config/db.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Algorithm to get personalized feed
function getPersonalizedFeed($pdo, $user_id = null, $page = 1, $limit = 10)
{
    $offset = ($page - 1) * $limit;

    // Convert limit and offset to integers for binding
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

// Get current page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$posts = getPersonalizedFeed($pdo, $user_id, $page, 10);

// Track view for recommendation algorithm
if ($user_id && !empty($posts)) {
    foreach ($posts as $post) {
        $stmt = $pdo->prepare("INSERT INTO user_behavior (user_id, action_type, target_id, target_type) VALUES (?, 'view', ?, 'post')");
        $stmt->execute([$user_id, $post['id']]);
    }
}

// Helper function to format time
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
    <title>Spark - Your Personalized Feed</title>
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
                // Fetch active stories
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
                    <?php endforeach;
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
                                    <?php if ($post['content_score'] > 80): ?>
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

                        <!-- Post Stats -->
                        <div class="stats">
                            <span class="me-3">
                                <i class="bi bi-heart-fill"></i> <?php echo $post['likes']; ?>
                            </span>
                            <span class="me-3">
                                <i class="bi bi-chat"></i> <?php echo $post['comments']; ?>
                            </span>
                            <span>
                                <i class="bi bi-arrow-repeat"></i> <?php echo $post['reposts']; ?>
                            </span>
                        </div>


                        <!-- Post Actions -->
                        <div class="post-actions">
                            <button class="action-btn like-btn <?php echo $post['user_liked'] ? 'liked' : ''; ?>"
                                onclick="reactToPost(<?php echo $post['id']; ?>, 'like')" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                                <i class="bi bi-heart<?php echo $post['user_liked'] ? '-fill' : ''; ?>"></i>
                            </button>

                            <button class="action-btn" onclick="toggleComments(<?php echo $post['id']; ?>)" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                                <i class="bi bi-chat"></i>
                            </button>

                            <!-- Repost Button -->
                            <button class="action-btn repost-btn" onclick="repost(<?php echo $post['id']; ?>)" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                                <i class="bi bi-arrow-repeat"></i>
                                <span class="repost-count"><?php echo $post['reposts']; ?></span>
                            </button>

                            <button class="action-btn" onclick="sharePost(<?php echo $post['id']; ?>)" <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                                <i class="bi bi-share"></i>
                            </button>
                        </div>

                        <!-- Comments Section (Initially Hidden) -->
                        <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display: none;">
                            <!-- Comments will be loaded via AJAX -->
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

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                url: 'load_posts.php',
                method: 'GET',
                data: { page: currentPage },
                success: function (response) {
                    if (response.posts && response.posts.length > 0) {
                        $('#feed-items').append(response.html);
                        hasMore = response.hasMore;
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
                    // Update like count display
                    const feedItem = btn.closest('.feed-item');
                    const likesCount = feedItem.find('.stats span:first-child');

                    if (type === 'like') {
                        if (response.action === 'added' || response.action === 'updated') {
                            btn.addClass('liked').find('i').removeClass('bi-heart').addClass('bi-heart-fill');
                            // Add pulse animation
                            btn.find('i').css('animation', 'heartBeat 0.6s ease');
                            setTimeout(() => {
                                btn.find('i').css('animation', '');
                            }, 600);
                        } else {
                            btn.removeClass('liked').find('i').removeClass('bi-heart-fill').addClass('bi-heart');
                        }
                        likesCount.html('<i class="bi bi-heart-fill"></i> ' + response.likes);
                    }

                    // Show success feedback
                    showToast('Post ' + type + 'd successfully!', 'success');
                }
            }, 'json').fail(function (xhr, status, error) {
                console.error('Error:', error);
                showToast('Error reacting to post. Please try again.', 'error');
            });
        }
        // Repost functionality
        function repost(postId) {
            <?php if (!$is_logged_in): ?>
                window.location.href = 'login.php';
                return;
            <?php endif; ?>

            const btn = $(`.repost-btn[onclick*="repost(${postId})"]`);

            $.post('repost.php', {
                post_id: postId
            }, function (response) {
                if (response.success) {
                    // Update repost count
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

                    // Also update in stats section
                    const feedItem = btn.closest('.feed-item');
                    const statsRepost = feedItem.find('.stats span:last-child');
                    if (statsRepost.length) {
                        statsRepost.html('<i class="bi bi-share"></i> ' + response.repost_count);
                    }
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

            if (commentsSection.is(':visible')) {
                commentsSection.hide();
            } else {
                if (commentsSection.html().trim() === '') {
                    loadComments(postId);
                }
                commentsSection.show();
            }
        }

        function loadComments(postId) {
            $('#comments-' + postId).html('<div class="text-center p-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading comments...</p></div>');

            $.get('fetch_comments.php', { post_id: postId }, function (html) {
                $('#comments-' + postId).html(html);
                initializeCommentEvents();
            }).fail(function (xhr, status, error) {
                console.error('Error loading comments:', error);
                $('#comments-' + postId).html('<p class="text-danger p-3">Error loading comments. Please try again.</p>');
            });
        }

        // Initialize comment form events
        function initializeCommentEvents() {
            console.log('Initializing comment events...');

            // Add comment form
            $(document).off('submit', '.add-comment-form').on('submit', '.add-comment-form', function (e) {
                e.preventDefault();
                console.log('Add comment form submitted');

                const form = $(this);
                const postId = form.data('post-id');
                const content = form.find('input[type="text"]').val().trim();

                if (!content) {
                    showToast('Please enter a comment', 'error');
                    return;
                }

                $.post('add_comment.php', {
                    post_id: postId,
                    content: content
                }, function (response) {
                    if (response.success) {
                        form.find('input[type="text"]').val('');
                        loadComments(postId); // Reload comments
                        showToast('Comment added successfully!', 'success');

                        // Update comment count
                        const feedItem = form.closest('.feed-item');
                        const commentCount = feedItem.find('.stats span:nth-child(2)');
                        const currentCount = parseInt(commentCount.text()) || 0;
                        commentCount.html('<i class="bi bi-chat"></i> ' + (currentCount + 1));
                    } else {
                        showToast(response.message || 'Error adding comment', 'error');
                    }
                }, 'json').fail(function (xhr, status, error) {
                    console.error('Error adding comment:', error);
                    showToast('Error adding comment. Please try again.', 'error');
                });
            });

            // Reply form
            $(document).off('submit', '.reply-comment-form').on('submit', '.reply-comment-form', function (e) {
                e.preventDefault();
                console.log('Reply form submitted');

                const form = $(this);
                const commentId = form.data('comment-id');
                const content = form.find('input[type="text"]').val().trim();

                if (!content) {
                    showToast('Please enter a reply', 'error');
                    return;
                }

                $.post('add_comment.php', {
                    parent_comment_id: commentId,
                    content: content
                }, function (response) {
                    if (response.success) {
                        form.find('input[type="text"]').val('');
                        form.closest('.reply-form').hide();

                        // Reload the comments
                        const postId = form.closest('.feed-item').data('post-id');
                        loadComments(postId);

                        showToast('Reply added successfully!', 'success');
                    } else {
                        showToast(response.message || 'Error adding reply', 'error');
                    }
                }, 'json').fail(function (xhr, status, error) {
                    console.error('Error adding reply:', error);
                    showToast('Error adding reply. Please try again.', 'error');
                });
            });

            // Reply button click
            $(document).off('click', '.reply-btn').on('click', '.reply-btn', function () {
                const commentId = $(this).data('comment-id');
                $('#reply-form-' + commentId).show();
            });
        }

        // Share Post with Proper Share URL
        function sharePost(postId) {
            <?php if (!$is_logged_in): ?>
                window.location.href = 'login.php';
                return;
            <?php endif; ?>

            // Create shareable URL that tracks the share
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
                    console.log('Error sharing:', err);
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
                console.error('Could not copy text: ', err);
                // Fallback: show text in alert
                alert('Share this link: ' + text);
                updateShareCount(postId);
            });
        }

        // Update share count visually
        function updateShareCount(postId) {
            // Send AJAX to update share count in database
            $.post('update_share_count.php', { post_id: postId })
                .fail(function () {
                    console.log('Failed to track share');
                });
        }

        // Toast notification function
        function showToast(message, type = 'success') {
            // Remove existing toasts
            $('.toast').remove();

            const toast = $(`
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `);

            $('body').append(toast);
            const toastInstance = new bootstrap.Toast(toast[0]);
            toastInstance.show();

            // Remove toast after it's hidden
            toast.on('hidden.bs.toast', function () {
                $(this).remove();
            });
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
        document.addEventListener('touchstart', e => {
            startY = e.touches[0].clientY;
        });

        document.addEventListener('touchend', e => {
            if (!startY) return;

            const endY = e.changedTouches[0].clientY;
            const diff = startY - endY;

            if (diff > 50) { // Swipe up
                loadMorePosts();
            }

            startY = null;
        });
    </script>
</body>

</html>
<?php include 'includes/footer.php'; ?>