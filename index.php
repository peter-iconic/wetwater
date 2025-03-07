<?php
// Include the database connection
include 'config/db.php';

// Include the header
include 'includes/header.php';

// Fetch all posts from the database
$stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch approved ads within their scheduled time
$current_time = date('Y-m-d H:i:s');
$stmt = $pdo->prepare("SELECT * FROM ads WHERE status = 'approved' AND start_time <= ? AND end_time >= ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$current_time, $current_time]);
$ad = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h1 class="text-center mb-4">Feed</h1>

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
                        <button class="btn btn-sm btn-outline-primary like-btn" data-post-id="<?php echo $post['id']; ?>">
                            👍 <?php echo $like_count; ?>
                        </button>
                        <button class="btn btn-sm btn-outline-danger dislike-btn" data-post-id="<?php echo $post['id']; ?>">
                            👎 <?php echo $dislike_count; ?>
                        </button>
                    </div>

                    <!-- Share Button -->
                    <button class="btn btn-sm btn-outline-secondary share-btn" data-post-id="<?php echo $post['id']; ?>"
                        data-bs-toggle="modal" data-bs-target="#captionModal">
                        Share to Feed
                    </button>

                    <!-- Comments -->
                    <div class="mt-3">
                        <h6>Comments:</h6>
                        <?php
                        // Fetch comments for this post
                        $stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? AND comments.parent_comment_id IS NULL ORDER BY comments.created_at ASC");
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

                                        <!-- Replies -->
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
                                                            <p class="card-text"><?php echo htmlspecialchars($reply['content']); ?></p>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Reply Form -->
                                        <form action="add_comment.php" method="POST" class="mt-2">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <input type="hidden" name="parent_comment_id" value="<?php echo $comment['id']; ?>">
                                            <div class="input-group">
                                                <textarea name="content" class="form-control" placeholder="Write a reply..."
                                                    required></textarea>
                                                <button type="submit" class="btn btn-primary">Reply</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Comment Form -->
                    <form action="add_comment.php" method="POST" class="mt-3">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <div class="input-group">
                            <textarea name="content" class="form-control" placeholder="Write a comment..." required></textarea>
                            <button type="submit" class="btn btn-primary">Comment</button>
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

<!-- Caption Modal -->
<div class="modal fade" id="captionModal" tabindex="-1" aria-labelledby="captionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="captionModalLabel">Add a Caption</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="captionForm">
                    <div class="mb-3">
                        <label for="captionInput" class="form-label">Caption</label>
                        <textarea class="form-control" id="captionInput" rows="3" required></textarea>
                    </div>
                    <input type="hidden" id="postIdInput">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="shareWithCaptionBtn">Share</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery and custom JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/realtime.js"></script>

<?php include 'includes/footer.php'; ?>