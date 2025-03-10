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

// Fetch number of followers
$stmt = $pdo->prepare("SELECT COUNT(*) AS followers_count FROM followers WHERE following_id = ?");
$stmt->execute([$user_id]);
$followers_count = $stmt->fetch(PDO::FETCH_ASSOC)['followers_count'];

// Fetch number of users the user is following
$stmt = $pdo->prepare("SELECT COUNT(*) AS following_count FROM followers WHERE follower_id = ?");
$stmt->execute([$user_id]);
$following_count = $stmt->fetch(PDO::FETCH_ASSOC)['following_count'];
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

                    <!-- Bio -->
                    <?php if (!empty($user['bio'])): ?>
                        <p class="mt-3"><?php echo htmlspecialchars($user['bio']); ?></p>
                    <?php endif; ?>

                    <!-- Followers and Following Count -->
                    <div class="d-flex justify-content-around mt-3">
                        <div>
                            <h5><?php echo $followers_count; ?></h5>
                            <p class="text-muted">Followers</p>
                        </div>
                        <div>
                            <h5><?php echo $following_count; ?></h5>
                            <p class="text-muted">Following</p>
                        </div>
                    </div>

                    <!-- Edit Profile Button (only visible to the logged-in user) -->
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id): ?>
                        <a href="edit_profile.php" class="btn btn-secondary">Edit Profile</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Additional User Information -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">About Me</h5>
                    <?php if (!empty($user['full_name'])): ?>
                        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($user['location'])): ?>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($user['location']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($user['interests'])): ?>
                        <p><strong>Interests:</strong> <?php echo htmlspecialchars($user['interests']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($user['education'])): ?>
                        <p><strong>Education:</strong> <?php echo htmlspecialchars($user['education']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($user['work'])): ?>
                        <p><strong>Work:</strong> <?php echo htmlspecialchars($user['work']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($user['website'])): ?>
                        <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($user['website']); ?>"
                                target="_blank"><?php echo htmlspecialchars($user['website']); ?></a></p>
                    <?php endif; ?>
                    <?php if (!empty($user['phone'])): ?>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                    <?php endif; ?>
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
                                    <?php
                                    $image_path = "" . htmlspecialchars($post['image']);
                                    if (file_exists($image_path)) {
                                        echo '<img src="' . $image_path . '" alt="Post Image" class="img-fluid rounded" style="max-width: 100%; height: auto;">';
                                    } else {
                                        echo '<p class="text-danger">Image not found: ' . $image_path . '</p>'; // Debugging: Print error if image is missing
                                    }
                                    ?>
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