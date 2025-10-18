<?php
include 'includes/header.php';



// Redirect if the user is not an admin
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
include 'config/db.php';

// Fetch statistics
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$postCount = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$commentCount = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$adCount = $pdo->query("SELECT COUNT(*) FROM ads WHERE status = 'approved'")->fetchColumn();

// Fetch recent activities
$recentUsers = $pdo->query("SELECT username, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentPosts = $pdo->query("SELECT content, created_at FROM posts ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentComments = $pdo->query("SELECT content, created_at FROM comments ORDER BY created_at DESC LIMIT 5")->fetchAll();
$pendingAds = $pdo->query("SELECT title, created_at FROM ads WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Fetch notifications
$notifications = $pdo->query("SELECT message, created_at FROM notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Header -->

    <div class="container mt-5">
        <h1 class="text-center mb-4">Admin Dashboard</h1>

        <!-- Quick Statistics -->
        <div class="row">
            <!-- Users Card -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users"></i> Users</h5>
                        <p class="card-text">Total Users: <?php echo $userCount; ?></p>
                        <a href="admin_users.php" class="btn btn-primary">Manage Users</a>
                    </div>
                </div>
            </div>

            <!-- Posts Card -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-file-alt"></i> Posts</h5>
                        <p class="card-text">Total Posts: <?php echo $postCount; ?></p>
                        <a href="admin_posts.php" class="btn btn-primary">Manage Posts</a>
                    </div>
                </div>
            </div>

            <!-- Comments Card -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-comments"></i> Comments</h5>
                        <p class="card-text">Total Comments: <?php echo $commentCount; ?></p>
                        <a href="admin_comments.php" class="btn btn-primary">Manage Comments</a>
                    </div>
                </div>
            </div>

            <!-- Ads Card -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-ad"></i> Ads</h5>
                        <p class="card-text">Active Ads: <?php echo $adCount; ?></p>
                        <a href="admin_ads.php" class="btn btn-primary">Manage Ads</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <!-- Recent Users -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-user-plus"></i> Recent Users</h5>
                        <ul>
                            <?php foreach ($recentUsers as $user): ?>
                                <li><?php echo htmlspecialchars($user['username']); ?> (<?php echo $user['created_at']; ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Posts -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-file-alt"></i> Recent Posts</h5>
                        <ul>
                            <?php foreach ($recentPosts as $post): ?>
                                <li><?php echo htmlspecialchars(substr($post['content'], 0, 50)); ?>...</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Comments -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-comments"></i> Recent Comments</h5>
                        <ul>
                            <?php foreach ($recentComments as $comment): ?>
                                <li><?php echo htmlspecialchars(substr($comment['content'], 0, 50)); ?>...</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Pending Ads -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-ad"></i> Pending Ads</h5>
                        <ul>
                            <?php foreach ($pendingAds as $ad): ?>
                                <li><?php echo htmlspecialchars($ad['title']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-bell"></i> Notifications</h5>
                        <ul>
                            <?php foreach ($notifications as $notification): ?>
                                <li><?php echo htmlspecialchars($notification['message']); ?>
                                    (<?php echo $notification['created_at']; ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>