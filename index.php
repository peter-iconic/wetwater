<?php
// Include the database connection
include 'config/db.php';

// Include the header
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle reactions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['react'])) {
    $post_id = $_POST['post_id'];
    $type = $_POST['type']; // 'like' or 'dislike'
    $user_id = $_SESSION['user_id'];

    // Fetch the username of the user who reacted
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $username = $stmt->fetch(PDO::FETCH_ASSOC)['username'];

    // Insert reaction
    $stmt = $pdo->prepare("INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $type]);

    // Fetch post owner
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post_owner = $stmt->fetch(PDO::FETCH_ASSOC)['user_id'];

    // Insert notification for the post owner
    $message = "$username reacted to your post with a $type.";
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
    $stmt->execute([$post_owner, $message]);
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

// Fetch all posts from the database
$stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch approved ads within their scheduled time
$current_time = date('Y-m-d H:i:s');
$stmt = $pdo->prepare("SELECT * FROM ads WHERE status = 'approved' AND start_time <= ? AND end_time >= ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$current_time, $current_time]);
$ad = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch active stories (not expired)
$stmt = $pdo->prepare("
    SELECT stories.*, users.username, users.profile_picture
    FROM stories
    JOIN users ON stories.user_id = users.id
    WHERE stories.expires_at > NOW()
    ORDER BY stories.created_at DESC
");
$stmt->execute();
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Main Container -->
<div class="container-fluid">
    <div class="row">
        <!-- Left Sidebar (Hidden on Mobile) -->
        <div class="col-md-3 d-none d-md-block">
            <!-- Trending Topics -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Trending Topics</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none">#SocialMedia</a></li>
                        <li><a href="#" class="text-decoration-none">#TechNews</a></li>
                        <li><a href="#" class="text-decoration-none">#Programming</a></li>
                        <li><a href="#" class="text-decoration-none">#AI</a></li>
                        <li><a href="#" class="text-decoration-none">#WebDevelopment</a></li>
                    </ul>
                </div>
            </div>

            <!-- Hashtags -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Popular Hashtags</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none">#SocialLife</a></li>
                        <li><a href="#" class="text-decoration-none">#TechTrends</a></li>
                        <li><a href="#" class="text-decoration-none">#Coding</a></li>
                        <li><a href="#" class="text-decoration-none">#Design</a></li>
                        <li><a href="#" class="text-decoration-none">#Innovation</a></li>
                    </ul>
                </div>
            </div>

            <!-- Online Friends -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Online Friends</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none">User 1</a></li>
                        <li><a href="#" class="text-decoration-none">User 2</a></li>
                        <li><a href="#" class="text-decoration-none">User 3</a></li>
                        <li><a href="#" class="text-decoration-none">User 4</a></li>
                        <li><a href="#" class="text-decoration-none">User 5</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Center Content -->
        <div class="col-md-6">
            <!-- Stories Section -->
            <div class="stories-container mb-4">
                <div class="d-flex overflow-auto">
                    <!-- Add to Story Button -->
                    <div class="story-card me-3">
                        <a href="create_story.php"
                            class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
                            <i class="bi bi-plus-lg" style="font-size: 2rem;"></i>
                        </a>
                        <p class="text-center mt-2">Add Story</p>
                    </div>

                    <!-- Display Stories -->
                    <?php if (!empty($stories)): ?>
                        <?php foreach ($stories as $story): ?>
                            <div class="story-card me-3">
                                <a href="view_story.php?id=<?php echo $story['id']; ?>">
                                    <img src="<?php echo htmlspecialchars($story['media_url']); ?>" alt="Story"
                                        class="rounded-circle" width="80" height="80">
                                    <p class="text-center"><?php echo htmlspecialchars($story['username']); ?></p>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Link to Create Post Page -->
            <div class="text-center mb-4">
                <a href="create_post.php" class="btn btn-primary">Create a New Post</a>
            </div>

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
                        <div class="card mb-3">
                            <div class="card-body">
                                <!-- Shared Post Indicator -->
                                <?php if (!empty($post['original_post_id'])): ?>
                                    <div class="text-muted mb-2">
                                        <small>Shared by <?php echo htmlspecialchars($post['username']); ?></small>
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

                                <!-- Reactions -->
                                <div class="mt-3">
                                    <?php
                                    // Fetch reactions for this post
                                    $stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM reactions WHERE post_id = ? AND type = 'like'");
                                    $stmt->execute([$post['id']]);
                                    $like_count = $stmt->fetch(PDO::FETCH_ASSOC)['like_count'];

                                    $stmt = $pdo->prepare("SELECT COUNT(*) as dislike_count FROM reactions WHERE post_id = ? AND type = 'dislike'");
                                    $stmt->execute([$post['id']]);
                                    $dislike_count = $stmt->fetch(PDO::FETCH_ASSOC)['dislike_count'];
                                    ?>
                                    <form action="" method="POST" style="display: inline;">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <input type="hidden" name="type" value="like">
                                        <button type="submit" name="react" class="btn btn-sm btn-outline-primary like-btn">
                                            👍 <?php echo $like_count; ?>
                                        </button>
                                    </form>
                                    <form action="" method="POST" style="display: inline;">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <input type="hidden" name="type" value="dislike">
                                        <button type="submit" name="react" class="btn btn-sm btn-outline-danger dislike-btn">
                                            👎 <?php echo $dislike_count; ?>
                                        </button>
                                    </form>
                                </div>

                                <!-- Share Button -->
                                <button class="btn btn-sm btn-outline-secondary share-btn"
                                    data-post-id="<?php echo $post['id']; ?>" data-bs-toggle="modal"
                                    data-bs-target="#captionModal">
                                    Share to Feed
                                </button>

                                <!-- Comments Section -->
                                <div class="mt-3">
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

                                                                        <!-- Replies to this reply (nested replies) -->
                                                                        <?php
                                                                        // Fetch replies to this reply
                                                                        $stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.parent_comment_id = ? ORDER BY comments.created_at ASC");
                                                                        $stmt->execute([$reply['id']]);
                                                                        $nested_replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                        if (!empty($nested_replies)): ?>
                                                                            <div class="ms-4 mt-2">
                                                                                <h6>Replies:</h6>
                                                                                <?php foreach ($nested_replies as $nested_reply): ?>
                                                                                    <div class="card mb-2">
                                                                                        <div class="card-body">
                                                                                            <h6 class="card-subtitle mb-2 text-muted">
                                                                                                <a
                                                                                                    href="profile.php?id=<?php echo $nested_reply['user_id']; ?>">
                                                                                                    <?php echo htmlspecialchars($nested_reply['username']); ?>
                                                                                                </a>
                                                                                                <small><?php echo $nested_reply['created_at']; ?></small>
                                                                                            </h6>
                                                                                            <p class="card-text">
                                                                                                <?php echo htmlspecialchars($nested_reply['content']); ?>
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php endforeach; ?>
                                                                            </div>
                                                                        <?php endif; ?>

                                                                        <!-- Reply Form for this reply -->
                                                                        <form action="" method="POST" class="mt-2">
                                                                            <input type="hidden" name="post_id"
                                                                                value="<?php echo $post['id']; ?>">
                                                                            <input type="hidden" name="parent_comment_id"
                                                                                value="<?php echo $reply['id']; ?>">
                                                                            <div class="input-group">
                                                                                <textarea name="content" class="form-control"
                                                                                    placeholder="Write a reply..." required></textarea>
                                                                                <button type="submit" name="reply"
                                                                                    class="btn btn-primary">Reply</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Reply Form for this comment -->
                                                    <form action="" method="POST" class="mt-2">
                                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                                        <input type="hidden" name="parent_comment_id"
                                                            value="<?php echo $comment['id']; ?>">
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
                                </div>

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

        <!-- Right Sidebar (Hidden on Mobile) -->
        <div class="col-md-3 d-none d-md-block">
            <!-- Ads -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Sponsored</h5>
                    <img src="assets/images/ad.jpg" alt="Ad" class="img-fluid rounded">
                    <p class="card-text">Check out this amazing offer!</p>
                    <a href="#" class="btn btn-primary">Learn More</a>
                </div>
            </div>

            <!-- Popular Posts -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Popular Posts</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none">Post 1</a></li>
                        <li><a href="#" class="text-decoration-none">Post 2</a></li>
                        <li><a href="#" class="text-decoration-none">Post 3</a></li>
                        <li><a href="#" class="text-decoration-none">Post 4</a></li>
                        <li><a href="#" class="text-decoration-none">Post 5</a></li>
                    </ul>
                </div>
            </div>

            <!-- Friend Suggestions -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Friend Suggestions</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none">User 1</a></li>
                        <li><a href="#" class="text-decoration-none">User 2</a></li>
                        <li><a href="#" class="text-decoration-none">User 3</a></li>
                        <li><a href="#" class="text-decoration-none">User 4</a></li>
                        <li><a href="#" class="text-decoration-none">User 5</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery and custom JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>