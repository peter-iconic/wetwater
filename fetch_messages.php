<?php
include 'config/db.php';

// Start the session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the receiver ID from the query string
if (!isset($_GET['receiver_id']) || !is_numeric($_GET['receiver_id'])) {
    echo "Invalid receiver ID.";
    exit();
}
$receiver_id = intval($_GET['receiver_id']);

// Fetch messages between the logged-in user and the other user
$stmt = $pdo->prepare("
    SELECT messages.*, users.username as sender_username, users.profile_picture
    FROM messages
    JOIN users ON messages.sender_id = users.id
    WHERE (messages.sender_id = ? AND messages.receiver_id = ?) OR (messages.sender_id = ? AND messages.receiver_id = ?)
    ORDER BY messages.created_at ASC
");
$stmt->execute([$_SESSION['user_id'], $receiver_id, $receiver_id, $_SESSION['user_id']]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Loop through messages and generate HTML
foreach ($messages as $message) {
    $is_sender = ($message['sender_id'] == $_SESSION['user_id']);
    $message_class = $is_sender ? 'bg-primary text-white' : 'bg-light';
    $alignment_class = $is_sender ? 'justify-content-end' : 'justify-content-start';
    ?>
    <div class="d-flex <?php echo $alignment_class; ?> mb-3">
        <div class="me-3">
            <a href="profile.php?id=<?php echo $message['sender_id']; ?>">
                <img src="assets/images/<?php echo htmlspecialchars($message['profile_picture'] ?? 'default_profile.jpg'); ?>"
                    alt="Profile Picture" class="rounded-circle" width="40" height="40">
            </a>
        </div>
        <div class="card <?php echo $message_class; ?>" style="max-width: 70%;">
            <div class="card-body">
                <h6 class="card-subtitle mb-2">
                    <a href="profile.php?id=<?php echo $message['sender_id']; ?>"
                        class="text-decoration-none <?php echo $is_sender ? 'text-white' : 'text-dark'; ?>">
                        <?php echo htmlspecialchars($message['sender_username']); ?>
                    </a>
                    <small class="text-muted"><?php echo $message['created_at']; ?></small>
                </h6>
                <p class="card-text"><?php echo htmlspecialchars($message['sender_message']); ?></p>
                <!-- Message Status Indicators -->
                <small class="text-muted">
                    <?php
                    if ($is_sender) {
                        echo '<i class="bi bi-check"></i> Sent';
                        if ($message['is_delivered']) {
                            echo ' <i class="bi bi-check-all"></i> Delivered';
                        }
                        if ($message['is_seen']) {
                            echo ' <i class="bi bi-eye"></i> Seen';
                        }
                    }
                    ?>
                </small>
            </div>
        </div>
    </div>
    <?php
}
?>