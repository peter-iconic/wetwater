<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    die("User ID not specified.");
}

$user_id = $_GET['id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Fetch user's posts with additional data
$stmt = $pdo->prepare("
    SELECT 
        posts.*,
        users.username,
        (SELECT COUNT(*) FROM reactions WHERE reactions.post_id = posts.id AND type = 'like') AS like_count,
        (SELECT COUNT(*) FROM reactions WHERE reactions.post_id = posts.id AND type = 'dislike') AS dislike_count,
        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count,
        (SELECT COUNT(*) FROM posts AS reposts WHERE reposts.original_post_id = posts.id) AS repost_count
    FROM posts 
    JOIN users ON posts.user_id = users.id
    WHERE posts.user_id = ? 
    ORDER BY posts.created_at DESC
");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch followers/following counts
$stmt = $pdo->prepare("SELECT COUNT(*) AS followers_count FROM followers WHERE following_id = ?");
$stmt->execute([$user_id]);
$followers_count = $stmt->fetch(PDO::FETCH_ASSOC)['followers_count'];

$stmt = $pdo->prepare("SELECT COUNT(*) AS following_count FROM followers WHERE follower_id = ?");
$stmt->execute([$user_id]);
$following_count = $stmt->fetch(PDO::FETCH_ASSOC)['following_count'];
?>

<div class="container mt-4" style="padding-top: 40px; padding-bottom: 80px;">
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-4">
            <div class="card mb-4 text-center">
                <div class="card-body">
                    <img src="assets/images/<?php echo htmlspecialchars($user['profile_picture']); ?>"
                        class="rounded-circle mb-3" width="150" height="150">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>

                    <div class="d-flex justify-content-around mb-3">
                        <div>
                            <a href="#" class="text-decoration-none" data-bs-toggle="modal"
                                data-bs-target="#followersModal">
                                <h5 class="mb-0"><?php echo $followers_count; ?></h5>
                                <p class="text-muted">Followers</p>
                            </a>
                        </div>
                        <div>
                            <a href="#" class="text-decoration-none" data-bs-toggle="modal"
                                data-bs-target="#followingModal">
                                <h5 class="mb-0"><?php echo $following_count; ?></h5>
                                <p class="text-muted">Following</p>
                            </a>
                        </div>
                    </div>

                    <?php if (!empty($user['bio'])): ?>
                        <p><?php echo htmlspecialchars($user['bio']); ?></p>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id): ?>
                        <a href="edit_profile.php" class="btn btn-outline-primary mb-2">Edit Profile</a>
                        <a href="logout.php" class="btn btn-danger">Logout</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5>Details</h5>
                    <?php if (!empty($user['location'])): ?>
                        <p><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($user['location']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($user['website'])): ?>
                        <p><i class="bi bi-link-45deg"></i> <a href="<?php echo htmlspecialchars($user['website']); ?>"
                                target="_blank">Website</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column - Posts -->
        <div class="col-md-8">
            <h2 class="mb-4">Posts</h2>

            <?php if (empty($posts)): ?>
                <div class="alert alert-info">No posts yet.</div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <?php foreach ($posts as $post): ?>
                        <div class="col">
                            <div class="card h-100">
                                <?php if (!empty($post['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($post['image']); ?>" class="card-img-top"
                                        alt="Post image">
                                <?php endif; ?>
                                <div class="card-body">
                                    <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                                    <small
                                        class="text-muted"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></small>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-outline-primary like-btn"
                                            data-post-id="<?php echo $post['id']; ?>">
                                            ❤️ <span class="like-count"><?php echo $post['like_count']; ?></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary toggle-comments"
                                            data-post-id="<?php echo $post['id']; ?>">
                                            💬 <span class="comment-count"><?php echo $post['comment_count']; ?></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success repost-btn"
                                            data-post-id="<?php echo $post['id']; ?>">
                                            🔄 <span class="repost-count"><?php echo $post['repost_count']; ?></span>
                                        </button>
                                    </div>

                                    <div class="comments-section mt-3" id="comments-<?php echo $post['id']; ?>"
                                        style="display: none;">
                                        <?php
                                        $stmt = $pdo->prepare("
                                            SELECT comments.*, users.username, users.profile_picture 
                                            FROM comments 
                                            JOIN users ON comments.user_id = users.id 
                                            WHERE post_id = ? 
                                            ORDER BY created_at DESC 
                                            LIMIT 3
                                        ");
                                        $stmt->execute([$post['id']]);
                                        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        ?>

                                        <?php foreach ($comments as $comment): ?>
                                            <div class="d-flex mb-2">
                                                <img src="assets/images/<?php echo htmlspecialchars($comment['profile_picture']); ?>"
                                                    class="rounded-circle me-2" width="32" height="32">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                                    <p class="mb-0"><?php echo htmlspecialchars($comment['content']); ?></p>
                                                    <small
                                                        class="text-muted"><?php echo date('M j', strtotime($comment['created_at'])); ?></small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                        <form class="comment-form mt-2" data-post-id="<?php echo $post['id']; ?>">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <div class="input-group">
                                                <input type="text" name="content" class="form-control"
                                                    placeholder="Add a comment...">
                                                <button type="submit" class="btn btn-primary">Post</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Followers Modal -->
<div class="modal fade" id="followersModal" tabindex="-1" aria-labelledby="followersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followersModalLabel">Followers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                $stmt = $pdo->prepare("
                    SELECT users.id, users.username, users.profile_picture 
                    FROM followers 
                    JOIN users ON followers.follower_id = users.id 
                    WHERE following_id = ?
                ");
                $stmt->execute([$user_id]);
                $followers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php if (empty($followers)): ?>
                    <p>No followers yet.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($followers as $follower): ?>
                            <a href="profile.php?id=<?php echo $follower['id']; ?>"
                                class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <img src="assets/images/<?php echo htmlspecialchars($follower['profile_picture']); ?>"
                                        class="rounded-circle me-3" width="40" height="40">
                                    <span><?php echo htmlspecialchars($follower['username']); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Following Modal -->
<div class="modal fade" id="followingModal" tabindex="-1" aria-labelledby="followingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followingModalLabel">Following</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                $stmt = $pdo->prepare("
                    SELECT users.id, users.username, users.profile_picture 
                    FROM followers 
                    JOIN users ON followers.following_id = users.id 
                    WHERE follower_id = ?
                ");
                $stmt->execute([$user_id]);
                $following = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php if (empty($following)): ?>
                    <p>Not following anyone yet.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($following as $followed_user): ?>
                            <a href="profile.php?id=<?php echo $followed_user['id']; ?>"
                                class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <img src="assets/images/<?php echo htmlspecialchars($followed_user['profile_picture']); ?>"
                                        class="rounded-circle me-3" width="40" height="40">
                                    <span><?php echo htmlspecialchars($followed_user['username']); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
<script>
    // jQuery-based toggling of comments section
    document.querySelectorAll('.toggle-comments').forEach(btn => {
        btn.addEventListener('click', () => {
            const postId = btn.dataset.postId;
            const section = document.getElementById('comments-' + postId);
            section.style.display = (section.style.display === 'none') ? 'block' : 'none';
        });
    });
</script>