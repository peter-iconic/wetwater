<?php
include 'config/db.php';
session_start();

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

// Fetch notifications for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display notifications
if (empty($notifications)) {
    echo '<p class="text-center">No new notifications.</p>';
} else {
    foreach ($notifications as $notification) {
        echo '
        <div class="card mb-3">
            <div class="card-body">
                <p>' . htmlspecialchars($notification['message']) . '</p>
                <small class="text-muted">' . $notification['created_at'] . '</small>
            </div>
        </div>';
    }
}
?>