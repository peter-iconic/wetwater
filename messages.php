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
    SELECT 
        users.id, 
        users.username, 
        users.profile_picture, 
        MAX(messages.created_at) AS last_message_time,
        MAX(messages.sender_message) AS last_message
    FROM messages
    JOIN users ON (messages.sender_id = users.id OR messages.receiver_id = users.id)
    WHERE (messages.sender_id = ? OR messages.receiver_id = ?) AND users.id != ?
    GROUP BY users.id
    ORDER BY last_message_time DESC
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch online users (mutual followers who are online)
$stmt = $pdo->prepare("
    SELECT users.id, users.username, users.profile_picture
    FROM users
    JOIN followers AS f1 ON (users.id = f1.following_id)
    JOIN followers AS f2 ON (users.id = f2.follower_id)
    WHERE f1.follower_id = ? AND f2.following_id = ? AND users.last_activity > NOW() - INTERVAL 5 MINUTE
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$online_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch mutual followers (users who follow each other)
$stmt = $pdo->prepare("
    SELECT users.id, users.username, users.profile_picture
    FROM users
    JOIN followers AS f1 ON (users.id = f1.following_id)
    JOIN followers AS f2 ON (users.id = f2.follower_id)
    WHERE f1.follower_id = ? AND f2.following_id = ?
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$mutual_followers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle search for messages
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $stmt = $pdo->prepare("
        SELECT 
            users.id, 
            users.username, 
            users.profile_picture, 
            MAX(messages.created_at) AS last_message_time,
            MAX(messages.sender_message) AS last_message
        FROM messages
        JOIN users ON (messages.sender_id = users.id OR messages.receiver_id = users.id)
        WHERE (messages.sender_id = ? OR messages.receiver_id = ?) 
          AND users.id != ?
          AND (users.username LIKE ? OR messages.sender_message LIKE ?)
        GROUP BY users.id
        ORDER BY last_message_time DESC
    ");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], "%$search_query%", "%$search_query%"]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<!-- Search Bar -->
<div class="mb-4">
    <form method="GET" action="">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search messages or users..."
                value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
</div>

<!-- Display Online Friends (Mutual Followers) -->
<?php if (empty($online_users)): ?>
    <!-- Button to Start New Chat -->
    <div class="text-center mt-4">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newConversationModal">
            Start New Conversation
        </button>
    </div><?php else: ?>
    <div class="row">
        <?php foreach ($online_users as $user): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="assets/images/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default_profile.jpg'); ?>"
                                alt="Profile Picture" class="rounded-circle me-3" width="50" height="50">
                            <div>
                                <h5 class="card-title mb-0">
                                    <a href="conversation.php?id=<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </a>
                                </h5>
                                <small class="text-success">Online</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Display Conversations -->
<?php if (empty($conversations)): ?>
    <p class="text-center">No conversations yet.</p>
<?php else: ?>
    <div class="row">
        <?php foreach ($conversations as $conversation): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="assets/images/<?php echo htmlspecialchars($conversation['profile_picture'] ?? 'default_profile.jpg'); ?>"
                                alt="Profile Picture" class="rounded-circle me-3" width="50" height="50">
                            <div>
                                <h5 class="card-title mb-0">
                                    <a href="conversation.php?id=<?php echo $conversation['id']; ?>">
                                        <?php echo htmlspecialchars($conversation['username']); ?>
                                    </a>
                                </h5>
                                <small class="text-muted">
                                    Last: <?php echo htmlspecialchars($conversation['last_message']); ?>
                                </small>
                                <br>
                                <small class="text-muted">
                                    <?php echo $conversation['last_message_time']; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>






<!-- Modal for Starting New Conversation -->
<div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newConversationModalLabel">Start New Conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($mutual_followers)): ?>
                    <p>No mutual followers to start a conversation with.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($mutual_followers as $user): ?>
                            <li class="list-group-item">
                                <a href="conversation.php?id=<?php echo $user['id']; ?>" class="text-decoration-none text-dark">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default_profile.jpg'); ?>"
                                            alt="Profile Picture" class="rounded-circle me-3" width="50" height="50">
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($user['username']); ?></h6>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'includes/footer.php'; ?>