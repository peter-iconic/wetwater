<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch ad performance metrics
$stmt = $pdo->prepare("
    SELECT ads.title, 
           SUM(CASE WHEN ad_analytics.type = 'impression' THEN 1 ELSE 0 END) as impressions,
           SUM(CASE WHEN ad_analytics.type = 'click' THEN 1 ELSE 0 END) as clicks,
           COUNT(ad_billing.id) as revenue
    FROM ads
    LEFT JOIN ad_analytics ON ads.id = ad_analytics.ad_id
    LEFT JOIN ad_billing ON ads.id = ad_billing.ad_id
    WHERE ads.user_id = ?
    GROUP BY ads.id
");
$stmt->execute([$_SESSION['user_id']]);
$ad_metrics = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user engagement metrics
$stmt = $pdo->prepare("
    SELECT COUNT(posts.id) as posts,
           COUNT(comments.id) as comments,
           COUNT(reactions.id) as reactions
    FROM users
    LEFT JOIN posts ON users.id = posts.user_id
    LEFT JOIN comments ON users.id = comments.user_id
    LEFT JOIN reactions ON users.id = reactions.user_id
    WHERE users.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user_metrics = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h1 class="text-center mb-4">Advanced Analytics</h1>

<!-- Ad Performance Metrics -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Ad Performance</h5>
        <?php if (empty($ad_metrics)): ?>
            <p>No ads found.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Ad Title</th>
                        <th>Impressions</th>
                        <th>Clicks</th>
                        <th>CTR</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ad_metrics as $metric): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($metric['title']); ?></td>
                            <td><?php echo $metric['impressions']; ?></td>
                            <td><?php echo $metric['clicks']; ?></td>
                            <td><?php echo ($metric['impressions'] > 0) ? round(($metric['clicks'] / $metric['impressions']) * 100, 2) : 0; ?>%
                            </td>
                            <td>$<?php echo $metric['revenue']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- User Engagement Metrics -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">User Engagement</h5>
        <p><strong>Posts:</strong> <?php echo $user_metrics['posts']; ?></p>
        <p><strong>Comments:</strong> <?php echo $user_metrics['comments']; ?></p>
        <p><strong>Reactions:</strong> <?php echo $user_metrics['reactions']; ?></p>
    </div>
</div>

<!-- Include Chart.js for Visualizations -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Ad Performance Chart -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Ad Performance Over Time</h5>
        <canvas id="adPerformanceChart"></canvas>
    </div>
</div>

<script>
    // Fetch ad performance data
    fetch('fetch_ad_performance.php')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('adPerformanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.dates,
                    datasets: [{
                        label: 'Impressions',
                        data: data.impressions,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        fill: false
                    }, {
                        label: 'Clicks',
                        data: data.clicks,
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