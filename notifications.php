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
<script>
    // WebSocket connection for real-time notifications
    const socket = new WebSocket('ws://localhost:8080');

    socket.onopen = () => {
        console.log('Connected to the WebSocket server');
        // Register the user with the WebSocket server
        socket.send(JSON.stringify({
            type: 'register',
            userId: <?php echo $_SESSION['user_id']; ?>
        }));
    };

    socket.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.type === 'notification') {
            // Append the new notification to the feed
            $('#notification-feed').prepend(`
                <div class="card mb-3">
                    <div class="card-body">
                        <p>${data.message}</p>
                        <small class="text-muted">${new Date().toLocaleString()}</small>
                    </div>
                </div>
            `);
        }
    };

    socket.onerror = (error) => {
        console.error('WebSocket error:', error);
    };

    socket.onclose = () => {
        console.log('WebSocket connection closed');
    };
</script>

<?php include 'includes/footer.php'; ?>