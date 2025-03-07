<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Mark all notifications as read
$stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);

// Fetch notifications for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="text-center mb-4">Notifications</h1>

<!-- Display Notifications -->
<div id="notification-feed">
    <?php if (empty($notifications)): ?>
        <p class="text-center">No new notifications.</p>
    <?php else: ?>
        <?php foreach ($notifications as $notification): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small class="text-muted"><?php echo $notification['created_at']; ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Include jQuery and custom JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/realtime.js"></script>

<?php include 'includes/footer.php'; ?>