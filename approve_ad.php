<?php
include 'config/db.php';

// Redirect if the user is not an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Check if an ad ID is provided
if (!isset($_GET['id'])) {
    die("Ad ID not specified.");
}

$ad_id = $_GET['id'];

// Approve the ad
try {
    $stmt = $pdo->prepare("UPDATE ads SET status = 'approved' WHERE id = ?");
    $stmt->execute([$ad_id]);

    $_SESSION['success'] = "Ad approved successfully!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Failed to approve ad. Please try again.";
}

header("Location: admin_ads.php");
exit();
?>