<?php
include 'config/db.php';

// Check if an ad ID and redirect URL are provided
if (!isset($_GET['ad_id']) || !isset($_GET['redirect'])) {
    die("Invalid request.");
}

$ad_id = $_GET['ad_id'];
$redirect_url = urldecode($_GET['redirect']);

// Track click
try {
    $stmt = $pdo->prepare("INSERT INTO ad_analytics (ad_id, type) VALUES (?, 'click')");
    $stmt->execute([$ad_id]);

    // Fetch the ad and pricing plan
    $stmt = $pdo->prepare("SELECT ads.*, pricing_plans.* FROM ads JOIN pricing_plans ON ads.pricing_plan_id = pricing_plans.id WHERE ads.id = ?");
    $stmt->execute([$ad_id]);
    $ad = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ad && $ad['type'] === 'click') {
        // Calculate cost for this click
        $cost = $ad['rate'];

        // Insert billing record
        $stmt = $pdo->prepare("INSERT INTO ad_billing (ad_id, user_id, amount) VALUES (?, ?, ?)");
        $stmt->execute([$ad['id'], $ad['user_id'], $cost]);
    }
} catch (PDOException $e) {
    // Log the error (optional)
    error_log("Failed to track click: " . $e->getMessage());
}

// Redirect to the ad's link
header("Location: $redirect_url");
exit();
?>