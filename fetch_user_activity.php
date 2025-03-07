<?php
include 'config/db.php';

// Fetch user activity data for the last 7 days
$dates = [];
$new_users = [];
$active_users = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[] = $date;

    // Fetch new users
    $stmt = $pdo->prepare("SELECT COUNT(*) as new_users FROM users WHERE created_at LIKE ?");
    $stmt->execute(["$date%"]);
    $new_users[] = $stmt->fetch(PDO::FETCH_ASSOC)['new_users'];

    // Fetch active users
    $stmt = $pdo->prepare("SELECT COUNT(*) as active_users FROM users WHERE last_login LIKE ?");
    $stmt->execute(["$date%"]);
    $active_users[] = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'];
}

// Return data as JSON
echo json_encode([
    'dates' => $dates,
    'new_users' => $new_users,
    'active_users' => $active_users
]);
?>