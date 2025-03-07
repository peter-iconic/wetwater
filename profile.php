<?php
include 'config/db.php';
include 'includes/header.php';

// Check if a user ID is provided in the URL
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

// Fetch user's posts
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card">
                <div class="card-body text-center">
                    <img src="assets/images/<?php echo htmlspecialchars($user['profile_picture']); ?>"
                        alt="Profile Picture" class="rounded-circle mb-3" width="150" height="150">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                    <p>Member since: <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <!-- User's Posts -->
            <h2>Posts</h2>
            <?php if (empty($posts)): ?>
                <p>No posts yet.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                            <?php if (!empty($post['image'])): ?>
                                <div class="text-center mt-3">
                                    <img src="assets/images/posts/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image"
                                        class="img-fluid rounded" style="max-width: 100%; height: auto;">
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">Posted on: <?php echo $post['created_at']; ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>