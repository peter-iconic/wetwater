<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all conversations for the logged-in user
$stmt = $pdo->prepare("
    SELECT users.id, users.username, MAX(messages.created_at) AS last_message_time
    FROM messages
    JOIN users ON (messages.sender_id = users.id OR messages.receiver_id = users.id)
    WHERE (messages.sender_id = ? OR messages.receiver_id = ?) AND users.id != ?
    GROUP BY users.id
    ORDER BY last_message_time DESC
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch online users (friends who are online)
$stmt = $pdo->prepare("
    SELECT users.id, users.username
    FROM users
    JOIN friends ON (users.id = friends.friend_id)
    WHERE friends.user_id = ? AND users.last_activity > NOW() - INTERVAL 5 MINUTE
");
$stmt->execute([$_SESSION['user_id']]);
$online_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="text-center mb-4">Messages</h1>

<!-- Display Online Users -->
<h3>Online Friends</h3>
<?php if (empty($online_users)): ?>
    <p class="text-center">No friends online.</p>
<?php else: ?>
    <?php foreach ($online_users as $user): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <a href="conversation.php?id=<?php echo $user['id']; ?>">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </a>
                </h5>
                <small class="text-success">Online</small>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Display Conversations -->
<h3>Previous Conversations</h3>
<?php if (empty($conversations)): ?>
    <p class="text-center">No conversations yet.</p>
<?php else: ?>
    <?php foreach ($conversations as $conversation): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <a href="conversation.php?id=<?php echo $conversation['id']; ?>">
                        <?php echo htmlspecialchars($conversation['username']); ?>
                    </a>
                </h5>
                <small class="text-muted">Last message: <?php echo $conversation['last_message_time']; ?></small>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>