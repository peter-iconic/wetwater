<?php
include 'config/db.php';
session_start();

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

// Get the other user's ID
$other_user_id = isset($_GET['other_user_id']) ? $_GET['other_user_id'] : null;

if (!$other_user_id) {
    die("User ID not specified.");
}

// Fetch messages between the logged-in user and the other user
$stmt = $pdo->prepare("
    SELECT * FROM messages
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$stmt->execute([$_SESSION['user_id'], $other_user_id, $other_user_id, $_SESSION['user_id']]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display messages
foreach ($messages as $message) {
    echo '
    <div class="mb-3">
        <div class="card">
            <div class="card-body">
                <p class="card-text">' . htmlspecialchars($message['message']) . '</p>
                <small class="text-muted">
                    Sent by ' . ($message['sender_id'] === $_SESSION['user_id'] ? 'You' : htmlspecialchars($other_user['username'])) . '
                    on ' . $message['created_at'] . '
                </small>
            </div>
        </div>
    </div>';
}
?>