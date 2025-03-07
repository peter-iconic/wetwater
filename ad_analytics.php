<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's ads
$stmt = $pdo->prepare("SELECT * FROM ads WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="text-center mb-4">Ad Analytics</h1>

<!-- Display Ads and Analytics -->
<?php if (empty($ads)): ?>
    <p class="text-center">No ads found.</p>
<?php else: ?>
    <?php foreach ($ads as $ad): ?>
        <?php
        // Fetch impressions and clicks for this ad
        $stmt = $pdo->prepare("SELECT type, COUNT(*) as count FROM ad_analytics WHERE ad_id = ? GROUP BY type");
        $stmt->execute([$ad['id']]);
        $analytics = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $impressions = 0;
        $clicks = 0;
        foreach ($analytics as $metric) {
            if ($metric['type'] === 'impression') {
                $impressions = $metric['count'];
            } elseif ($metric['type'] === 'click') {
                $clicks = $metric['count'];
            }
        }
        ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($ad['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($ad['description']); ?></p>
                <p><strong>Impressions:</strong> <?php echo $impressions; ?></p>
                <p><strong>Clicks:</strong> <?php echo $clicks; ?></p>
                <p><strong>CTR (Click-Through Rate):</strong>
                    <?php echo ($impressions > 0) ? round(($clicks / $impressions) * 100, 2) : 0; ?>%</p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>