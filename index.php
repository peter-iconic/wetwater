<?php
// Include the header
include 'includes/header.php';


// Include the database connection
include 'config/db.php';



// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle reposts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['repost'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch the original post
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $original_post = $stmt->fetch(PDO::FETCH_ASSOC);

    // Create a repost
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, original_post_id, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, "Reposted", $post_id]);

    // Notification for the original poster
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $username = $stmt->fetch(PDO::FETCH_ASSOC)['username'];

    $message = "$username reposted your post";
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
    $stmt->execute([$original_post['user_id'], $message]);

    header("Location: index.php");
    exit();
}

// Handle reactions via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_react'])) {
    $post_id = $_POST['post_id'];
    $type = $_POST['type'];
    $user_id = $_SESSION['user_id'];

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

    // Get updated counts
    $stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM reactions WHERE post_id = ? AND type = 'like'");
    $stmt->execute([$post_id]);
    $like_count = $stmt->fetch(PDO::FETCH_ASSOC)['like_count'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as dislike_count FROM reactions WHERE post_id = ? AND type = 'dislike'");
    $stmt->execute([$post_id]);
    $dislike_count = $stmt->fetch(PDO::FETCH_ASSOC)['dislike_count'];

    // Check if user reacted
    $stmt = $pdo->prepare("SELECT type FROM reactions WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $user_reaction = $stmt->fetch(PDO::FETCH_ASSOC);

    // Send response
    header('Content-Type: application/json');
    echo json_encode([
        'like_count' => $like_count,
        'dislike_count' => $dislike_count,
        'user_reaction' => $user_reaction ? $user_reaction['type'] : null
    ]);
    exit();
}

// Handle comments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $post_id = $_POST['post_id'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // Fetch the username of the user who commented
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $username = $stmt->fetch(PDO::FETCH_ASSOC)['username'];

    // Insert comment
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $content]);

    // Fetch post owner
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post_owner = $stmt->fetch(PDO::FETCH_ASSOC)['user_id'];

    // Insert notification for the post owner
    $message = "$username commented on your post: " . substr($content, 0, 50) . "...";
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
    $stmt->execute([$post_owner, $message]);
}

// Handle replies
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $post_id = $_POST['post_id'];
    $comment_id = $_POST['parent_comment_id'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // Fetch the username of the user who replied
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $username = $stmt->fetch(PDO::FETCH_ASSOC)['username'];

    // Insert reply
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, parent_comment_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $content, $comment_id]);

    // Fetch comment owner
    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);
    $comment_owner = $stmt->fetch(PDO::FETCH_ASSOC)['user_id'];

    // Insert notification for the comment owner
    $message = "$username replied to your comment: " . substr($content, 0, 50) . "...";
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
    $stmt->execute([$comment_owner, $message]);
}

// Fetch all posts from the database with additional counts
$stmt = $pdo->query("
    SELECT 
        posts.*, 
        users.username,
        (SELECT COUNT(*) FROM posts AS reposts WHERE reposts.original_post_id = posts.id) AS repost_count,
        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    ORDER BY posts.created_at DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch approved ads within their scheduled time
$current_time = date('Y-m-d H:i:s');
$stmt = $pdo->prepare("SELECT * FROM ads WHERE status = 'approved' AND start_time <= ? AND end_time >= ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$current_time, $current_time]);
$ad = $stmt->fetch(PDO::FETCH_ASSOC);


?>






<!-- Center Content -->
<div class="col-md-6" style="padding-top: 40px; padding-bottom: 55px;">


    <!-- Display Approved Ad -->
    <?php if ($ad): ?>
        <?php
        // Track impression
        $stmt = $pdo->prepare("INSERT INTO ad_analytics (ad_id, type) VALUES (?, 'impression')");
        $stmt->execute([$ad['id']]);

        // Fetch the pricing plan
        $stmt = $pdo->prepare("SELECT * FROM pricing_plans WHERE id = ?");
        $stmt->execute([$ad['pricing_plan_id']]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($plan && $plan['type'] === 'impression') {
            // Calculate cost for this impression
            $cost = $plan['rate'];

            // Insert billing record
            $stmt = $pdo->prepare("INSERT INTO ad_billing (ad_id, user_id, amount) VALUES (?, ?, ?)");
            $stmt->execute([$ad['id'], $ad['user_id'], $cost]);
        }
        ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($ad['title']); ?></h5>
                <?php if ($ad['image']): ?>
                    <div class="text-center mb-3"> <!-- Center the ad image -->
                        <img src="<?php echo $ad['image']; ?>" alt="Ad Image" class="img-fluid rounded"
                            style="max-width: 100%; height: auto;">
                    </div>
                <?php endif; ?>
                <p class="card-text"><?php echo htmlspecialchars($ad['description']); ?></p>
                <a href="track_click.php?ad_id=<?php echo $ad['id']; ?>&redirect=<?php echo urlencode($ad['link']); ?>"
                    class="btn btn-primary" target="_blank">Learn More</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Display Posts -->
    <div id="post-feed">
        <?php if (empty($posts)): ?>
            <p class="text-center">No posts yet. Be the first to share!</p>
        <?php else: ?>
            <?php foreach ($posts as $index => $post): ?>
                <div class="card mb-3" id="post-<?php echo $post['id']; ?>">
                    <div class="card-body">
                        <!-- Shared Post Indicator -->
                        <?php if (!empty($post['original_post_id'])): ?>
                            <div class="text-muted mb-2">
                                <small>Reposted by <?php echo htmlspecialchars($post['username']); ?></small>
                            </div>
                            <?php
                            // Fetch the original post
                            $stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
                            $stmt->execute([$post['original_post_id']]);
                            $original_post = $stmt->fetch(PDO::FETCH_ASSOC);
                            ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        Original post by <a href="profile.php?id=<?php echo $original_post['user_id']; ?>">
                                            <?php echo htmlspecialchars($original_post['username']); ?>
                                        </a>
                                    </h6>
                                    <p class="card-text"><?php echo htmlspecialchars($original_post['content']); ?></p>
                                    <?php if (!empty($original_post['image'])): ?>
                                        <div class="text-center mb-3">
                                            <img src="<?php echo $original_post['image']; ?>" alt="Original Post Image"
                                                class="img-fluid rounded" style="max-width: 100%; height: auto;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Post content -->
                        <h5 class="card-title">
                            <a href="profile.php?id=<?php echo $post['user_id']; ?>">
                                <?php echo htmlspecialchars($post['username']); ?>
                            </a>
                        </h5>
                        <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                        <?php if (!empty($post['caption'])): ?>
                            <div class="text-muted mb-3">
                                <small>Caption: <?php echo htmlspecialchars($post['caption']); ?></small>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($post['image'])): ?>
                            <div class="text-center mb-3"> <!-- Center the image -->
                                <img src="<?php echo $post['image']; ?>" alt="Post Image" class="img-fluid rounded"
                                    style="max-width: 100%; height: auto;">
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Posted on: <?php echo $post['created_at']; ?></small>

                        <!-- Reactions and Counts -->
                        <div class="mt-3 d-flex align-items-center">
                            <?php
                            // Fetch reactions for this post
                            $stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM reactions WHERE post_id = ? AND type = 'like'");
                            $stmt->execute([$post['id']]);
                            $like_count = $stmt->fetch(PDO::FETCH_ASSOC)['like_count'];

                            $stmt = $pdo->prepare("SELECT COUNT(*) as dislike_count FROM reactions WHERE post_id = ? AND type = 'dislike'");
                            $stmt->execute([$post['id']]);
                            $dislike_count = $stmt->fetch(PDO::FETCH_ASSOC)['dislike_count'];

                            // Check if current user has reacted
                            $user_reaction = null;
                            if (isset($_SESSION['user_id'])) {
                                $stmt = $pdo->prepare("SELECT type FROM reactions WHERE post_id = ? AND user_id = ?");
                                $stmt->execute([$post['id'], $_SESSION['user_id']]);
                                $user_reaction = $stmt->fetch(PDO::FETCH_ASSOC);
                            }
                            ?>



                            <!-- Like Button -->
                            <button class="btn btn-sm btn-outline-primary like-btn me-2"
                                data-post-id="<?php echo $post['id']; ?>" data-type="like" <?php if ($user_reaction && $user_reaction['type'] === 'like')
                                       echo 'style="background-color: #0d6efd; color: white;"'; ?>>
                                ❤️ <span class="like-count"><?php echo $like_count; ?></span>
                            </button>

                            <!-- Dislike Button -->
                            <button class="btn btn-sm btn-outline-danger dislike-btn me-2"
                                data-post-id="<?php echo $post['id']; ?>" data-type="dislike" <?php if ($user_reaction && $user_reaction['type'] === 'dislike')
                                       echo 'style="background-color: #dc3545; color: white;"'; ?>>
                                💔 <span class="dislike-count"><?php echo $dislike_count; ?></span>
                            </button>

                            <!-- Repost Button with Count -->
                            <form action="" method="POST" style="display: inline;" class="me-2">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" name="repost" class="btn btn-sm btn-outline-secondary">
                                    🔄 <span class="repost-count"><?php echo $post['repost_count']; ?></span>
                                </button>
                            </form>

                            <!-- Comment Toggle Button with Count -->
                            <button class="btn btn-sm btn-outline-info toggle-comments"
                                data-post-id="<?php echo $post['id']; ?>">
                                💬 <span class="comment-count"><?php echo $post['comment_count']; ?></span>
                            </button>
                        </div>

                        <!-- Comments Section (Initially Hidden) -->
                        <div class="comments-section mt-3" id="comments-<?php echo $post['id']; ?>" style="display: none;">
                            <h6>Comments:</h6>
                            <?php
                            // Fetch top-level comments (no parent_comment_id)
                            $stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? AND comments.parent_comment_id IS NULL ORDER BY comments.created_at ASC LIMIT 3");
                            $stmt->execute([$post['id']]);
                            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (empty($comments)): ?>
                                <p class="text-muted">No comments yet.</p>
                            <?php else: ?>
                                <?php foreach ($comments as $comment): ?>
                                    <div class="card mb-2">
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <a href="profile.php?id=<?php echo $comment['user_id']; ?>">
                                                    <?php echo htmlspecialchars($comment['username']); ?>
                                                </a>
                                                <small><?php echo $comment['created_at']; ?></small>
                                            </h6>
                                            <p class="card-text"><?php echo htmlspecialchars($comment['content']); ?></p>

                                            <!-- Replies to this comment -->
                                            <?php
                                            // Fetch replies for this comment
                                            $stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.parent_comment_id = ? ORDER BY comments.created_at ASC");
                                            $stmt->execute([$comment['id']]);
                                            $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if (!empty($replies)): ?>
                                                <div class="ms-4 mt-2">
                                                    <h6>Replies:</h6>
                                                    <?php foreach ($replies as $reply): ?>
                                                        <div class="card mb-2">
                                                            <div class="card-body">
                                                                <h6 class="card-subtitle mb-2 text-muted">
                                                                    <a href="profile.php?id=<?php echo $reply['user_id']; ?>">
                                                                        <?php echo htmlspecialchars($reply['username']); ?>
                                                                    </a>
                                                                    <small><?php echo $reply['created_at']; ?></small>
                                                                </h6>
                                                                <p class="card-text"><?php echo htmlspecialchars($reply['content']); ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Reply Form for this comment -->
                                            <form action="" method="POST" class="mt-2">
                                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                                <input type="hidden" name="parent_comment_id" value="<?php echo $comment['id']; ?>">
                                                <div class="input-group">
                                                    <textarea name="content" class="form-control" placeholder="Write a reply..."
                                                        required></textarea>
                                                    <button type="submit" name="reply" class="btn btn-primary">Reply</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <!-- View More Comments Button -->
                                <div class="text-center mt-3">
                                    <button class="btn btn-sm btn-outline-primary view-more-comments"
                                        data-post-id="<?php echo $post['id']; ?>">
                                        View More Comments
                                    </button>
                                </div>
                            <?php endif; ?>

                            <!-- Comment Form -->
                            <form action="" method="POST" class="mt-3">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <div class="input-group">
                                    <textarea name="content" class="form-control" placeholder="Write a comment..."
                                        required></textarea>
                                    <button type="submit" name="comment" class="btn btn-primary">Comment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Display an ad after every 3 posts -->
                <?php if (($index + 1) % 3 === 0): ?>
                    <?php
                    // Fetch another approved ad within its scheduled time
                    $stmt = $pdo->prepare("SELECT * FROM ads WHERE status = 'approved' AND start_time <= ? AND end_time >= ? ORDER BY created_at DESC LIMIT 1");
                    $stmt->execute([$current_time, $current_time]);
                    $ad = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <?php if ($ad): ?>
                        <?php
                        // Track impression
                        $stmt = $pdo->prepare("INSERT INTO ad_analytics (ad_id, type) VALUES (?, 'impression')");
                        $stmt->execute([$ad['id']]);

                        // Fetch the pricing plan
                        $stmt = $pdo->prepare("SELECT * FROM pricing_plans WHERE id = ?");
                        $stmt->execute([$ad['pricing_plan_id']]);
                        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($plan && $plan['type'] === 'impression') {
                            // Calculate cost for this impression
                            $cost = $plan['rate'];

                            // Insert billing record
                            $stmt = $pdo->prepare("INSERT INTO ad_billing (ad_id, user_id, amount) VALUES (?, ?, ?)");
                            $stmt->execute([$ad['id'], $ad['user_id'], $cost]);
                        }
                        ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($ad['title']); ?></h5>
                                <?php if ($ad['image']): ?>
                                    <div class="text-center mb-3"> <!-- Center the ad image -->
                                        <img src="<?php echo $ad['image']; ?>" alt="Ad Image" class="img-fluid rounded"
                                            style="max-width: 100%; height: auto;">
                                    </div>
                                <?php endif; ?>
                                <p class="card-text"><?php echo htmlspecialchars($ad['description']); ?></p>
                                <a href="track_click.php?ad_id=<?php echo $ad['id']; ?>&redirect=<?php echo urlencode($ad['link']); ?>"
                                    class="btn btn-primary" target="_blank">Learn More</a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>





<!-- Include jQuery and custom JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Handle reactions with AJAX
        $('.like-btn, .dislike-btn').on('click', function () {
            const post_id = $(this).data('post-id');
            const type = $(this).data('type');
            const button = $(this);
            const postContainer = button.closest('.card-body');

            $.ajax({
                url: 'index.php',
                method: 'POST',
                data: {
                    ajax_react: 1,
                    post_id: post_id,
                    type: type
                },
                dataType: 'json',
                success: function (response) {
                    // Update like and dislike counts
                    postContainer.find('.like-count').text(response.like_count);
                    postContainer.find('.dislike-count').text(response.dislike_count);

                    // Update button styles based on user reaction
                    const likeBtn = postContainer.find('.like-btn');
                    const dislikeBtn = postContainer.find('.dislike-btn');

                    // Reset both buttons
                    likeBtn.css({ 'background-color': '', 'color': '' });
                    dislikeBtn.css({ 'background-color': '', 'color': '' });

                    // Style the active button if user reacted
                    if (response.user_reaction === 'like') {
                        likeBtn.css({ 'background-color': '#0d6efd', 'color': 'white' });
                    } else if (response.user_reaction === 'dislike') {
                        dislikeBtn.css({ 'background-color': '#dc3545', 'color': 'white' });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error reacting to post:', error);
                    showToast('Error reacting to post. Please try again.', 'error');
                }
            });
        });

        // Toggle comments section
        $('.toggle-comments').on('click', function () {
            const post_id = $(this).data('post-id');
            const commentsSection = $('#comments-' + post_id);
            commentsSection.toggle();

            // Change icon based on visibility
            const icon = $(this).find('i');
            if (commentsSection.is(':visible')) {
                $(this).html('<i class="bi bi-chat-left-text-fill"></i> <span class="comment-count">' + $(this).find('.comment-count').text() + '</span>');
            } else {
                $(this).html('<i class="bi bi-chat-left"></i> <span class="comment-count">' + $(this).find('.comment-count').text() + '</span>');
            }
        });

        // View More Comments Button
        $(document).on('click', '.view-more-comments', function () {
            const post_id = $(this).data('post-id');
            const button = $(this);

            $.ajax({
                url: 'fetch_comments.php',
                method: 'GET',
                data: { post_id: post_id },
                success: function (response) {
                    // Append the fetched comments
                    button.closest('.mt-3').append(response);
                    // Hide the "View More Comments" button
                    button.hide();
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching comments:', error);
                    showToast('Error loading comments. Please try again.', 'error');
                }
            });
        });

        // Handle repost with AJAX
        $('form[name="repost"]').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const repostCount = form.find('.repost-count');

            $.ajax({
                url: 'index.php',
                method: 'POST',
                data: form.serialize(),
                success: function () {
                    // Increment the repost count
                    repostCount.text(parseInt(repostCount.text()) + 1);
                    // Show toast notification
                    showToast('Post reposted successfully!');
                },
                error: function (xhr, status, error) {
                    console.error('Error reposting:', error);
                    showToast('Error reposting. Please try again.', 'error');
                }
            });
        });

        // Handle comment submission with AJAX (updated to prevent page load)
        $(document).on('submit', 'form.comment-form', function (e) {
            e.preventDefault();
            const form = $(this);
            const postContainer = form.closest('.card-body');
            const commentCount = postContainer.find('.comment-count');
            const commentContent = form.find('textarea[name="content"]');
            const postId = form.find('input[name="post_id"]').val();

            if (commentContent.val().trim() === '') {
                showToast('Please enter a comment', 'error');
                return;
            }

            $.ajax({
                url: 'index.php',
                method: 'POST',
                data: form.serialize(),
                success: function (response) {
                    // Increment the comment count
                    commentCount.text(parseInt(commentCount.text()) + 1);
                    // Clear the comment box
                    commentContent.val('');
                    // Show success message
                    showToast('Comment posted successfully!');

                    // Refresh the comments section without reloading the page
                    $.ajax({
                        url: 'fetch_comments.php',
                        method: 'GET',
                        data: { post_id: postId },
                        success: function (commentsResponse) {
                            $('#comments-' + postId + ' .comments-list').html(commentsResponse);
                        },
                        error: function (xhr, status, error) {
                            console.error('Error refreshing comments:', error);
                        }
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error adding comment:', error);
                    showToast('Error posting comment. Please try again.', 'error');
                }
            });
        });

        // Toast notification function
        function showToast(message, type = 'success') {
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
    });
</script>

<?php include 'includes/footer.php'; ?>