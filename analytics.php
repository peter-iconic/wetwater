<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Fetch user statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

$stmt = $pdo->query("SELECT COUNT(*) as active_users FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$active_users = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'];

$stmt = $pdo->query("SELECT COUNT(*) as new_users FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$new_users = $stmt->fetch(PDO::FETCH_ASSOC)['new_users'];

// Fetch post statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_posts FROM posts");
$total_posts = $stmt->fetch(PDO::FETCH_ASSOC)['total_posts'];

$stmt = $pdo->query("SELECT COUNT(*) as posts_last_7_days FROM posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$posts_last_7_days = $stmt->fetch(PDO::FETCH_ASSOC)['posts_last_7_days'];

$stmt = $pdo->query("
    SELECT users.username, COUNT(posts.id) as post_count
    FROM posts
    JOIN users ON posts.user_id = users.id
    GROUP BY users.id
    ORDER BY post_count DESC
    LIMIT 5
");
$top_posters = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch comment statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_comments FROM comments");
$total_comments = $stmt->fetch(PDO::FETCH_ASSOC)['total_comments'];

$stmt = $pdo->query("SELECT COUNT(*) as comments_last_7_days FROM comments WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$comments_last_7_days = $stmt->fetch(PDO::FETCH_ASSOC)['comments_last_7_days'];

$stmt = $pdo->query("
    SELECT users.username, COUNT(comments.id) as comment_count
    FROM comments
    JOIN users ON comments.user_id = users.id
    GROUP BY users.id
    ORDER BY comment_count DESC
    LIMIT 5
");
$top_commenters = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="text-center mb-4">Analytics Dashboard</h1>

<!-- User Statistics -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">User Statistics</h5>
        <p>Total Users: <?php echo $total_users; ?></p>
        <p>Active Users (Last 7 Days): <?php echo $active_users; ?></p>
        <p>New Users (Last 7 Days): <?php echo $new_users; ?></p>
    </div>
</div>

<!-- Post Statistics -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Post Statistics</h5>
        <p>Total Posts: <?php echo $total_posts; ?></p>
        <p>Posts (Last 7 Days): <?php echo $posts_last_7_days; ?></p>
        <h6>Top Posters</h6>
        <ul>
            <?php foreach ($top_posters as $poster): ?>
                <li><?php echo htmlspecialchars($poster['username']); ?> - <?php echo $poster['post_count']; ?> posts</li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Comment Statistics -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Comment Statistics</h5>
        <p>Total Comments: <?php echo $total_comments; ?></p>
        <p>Comments (Last 7 Days): <?php echo $comments_last_7_days; ?></p>
        <h6>Top Commenters</h6>
        <ul>
            <?php foreach ($top_commenters as $commenter): ?>
                <li><?php echo htmlspecialchars($commenter['username']); ?> - <?php echo $commenter['comment_count']; ?>
                    comments</li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Include Chart.js for Visualizations -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- User Activity Chart -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">User Activity (Last 7 Days)</h5>
        <canvas id="userActivityChart"></canvas>
    </div>
</div>

<script>
    // Fetch user activity data
    fetch('fetch_user_activity.php')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('userActivityChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.dates,
                    datasets: [{
                        label: 'New Users',
                        data: data.new_users,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        fill: false
                    }, {
                        label: 'Active Users',
                        data: data.active_users,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
</script>

<?php include 'includes/footer.php'; ?>