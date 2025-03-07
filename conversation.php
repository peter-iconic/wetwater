<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the conversation ID from the URL
if (!isset($_GET['id'])) {
    header("Location: messages.php");
    exit();
}
$other_user_id = $_GET['id'];

// Fetch the other user's details
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
$stmt->execute([$other_user_id]);
$other_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$other_user) {
    header("Location: messages.php");
    exit();
}

// Fetch messages between the logged-in user and the other user
$stmt = $pdo->prepare("
    SELECT messages.*, users.username as sender_username
    FROM messages
    JOIN users ON messages.sender_id = users.id
    WHERE (messages.sender_id = ? AND messages.receiver_id = ?) OR (messages.sender_id = ? AND messages.receiver_id = ?)
    ORDER BY messages.created_at ASC
");
$stmt->execute([$_SESSION['user_id'], $other_user_id, $other_user_id, $_SESSION['user_id']]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="text-center mb-4">Conversation with <?php echo htmlspecialchars($other_user['username']); ?></h1>

<!-- Display Messages -->
<div id="conversation">
    <?php if (empty($messages)): ?>
        <p class="text-center">No messages yet.</p>
    <?php else: ?>
        <?php foreach ($messages as $message): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">
                        <?php echo htmlspecialchars($message['sender_username']); ?>
                        <small><?php echo $message['created_at']; ?></small>
                    </h6>
                    <p class="card-text"><?php echo htmlspecialchars($message['message']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Message Input Form -->
<form action="send_message.php" method="POST" class="mt-4">
    <input type="hidden" name="receiver_id" value="<?php echo $other_user_id; ?>">
    <div class="input-group">
        <textarea name="message" class="form-control" placeholder="Type your message..." required></textarea>
        <button type="submit" class="btn btn-primary">Send</button>
    </div>
</form>

<?php include 'includes/footer.php'; ?>