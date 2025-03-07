<?php
include 'config/db.php';

// Fetch ad performance data for the last 7 days
$dates = [];
$impressions = [];
$clicks = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[] = $date;

    // Fetch impressions
    $stmt = $pdo->prepare("SELECT COUNT(*) as impressions FROM ad_analytics WHERE type = 'impression' AND DATE(created_at) = ?");
    $stmt->execute([$date]);
    $impressions[] = $stmt->fetch(PDO::FETCH_ASSOC)['impressions'];

    // Fetch clicks
    $stmt = $pdo->prepare("SELECT COUNT(*) as clicks FROM ad_analytics WHERE type = 'click' AND DATE(created_at) = ?");
    $stmt->execute([$date]);
    $clicks[] = $stmt->fetch(PDO::FETCH_ASSOC)['clicks'];
}

// Return data as JSON
echo json_encode([
    'dates' => $dates,
    'impressions' => $impressions,
    'clicks' => $clicks
]);
?>