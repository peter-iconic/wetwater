<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's friends
$stmt = $pdo->prepare("
    SELECT users.id, users.username
    FROM friends
    JOIN users ON friends.friend_id = users.id
    WHERE friends.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch pending friend requests
$stmt = $pdo->prepare("
    SELECT friend_requests.id, users.username, users.id as sender_id
    FROM friend_requests
    JOIN users ON friend_requests.sender_id = users.id
    WHERE friend_requests.receiver_id = ? AND friend_requests.status = 'pending'
");
$stmt->execute([$_SESSION['user_id']]);
$pending_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch users who are not friends or have no pending requests
$stmt = $pdo->prepare("
    SELECT users.id, users.username
    FROM users
    WHERE users.id != ?
    AND users.id NOT IN (
        SELECT friend_id FROM friends WHERE user_id = ?
        UNION
        SELECT sender_id FROM friend_requests WHERE receiver_id = ?
        UNION
        SELECT receiver_id FROM friend_requests WHERE sender_id = ?
    )
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
$non_friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="text-center mb-4">Friends</h1>

<!-- Display Pending Friend Requests -->
<h3>Pending Friend Requests</h3>
<?php if (empty($pending_requests)): ?>
    <p>No pending friend requests.</p>
<?php else: ?>
    <?php foreach ($pending_requests as $request): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($request['username']); ?></h5>
                <form action="handle_friend_request.php" method="POST" style="display: inline;">
                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                    <input type="hidden" name="action" value="accept">
                    <button type="submit" class="btn btn-success btn-sm">Accept</button>
                </form>
                <form action="handle_friend_request.php" method="POST" style="display: inline;">
                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                    <input type="hidden" name="action" value="decline">
                    <button type="submit" class="btn btn-danger btn-sm">Decline</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Display Friends List -->
<h3>Your Friends</h3>
<?php if (empty($friends)): ?>
    <p>No friends yet.</p>
<?php else: ?>
    <?php foreach ($friends as $friend): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <a href="profile.php?id=<?php echo $friend['id']; ?>">
                        <?php echo htmlspecialchars($friend['username']); ?>
                    </a>
                </h5>
                <form action="handle_friend_request.php" method="POST" style="display: inline;">
                    <input type="hidden" name="friend_id" value="<?php echo $friend['id']; ?>">
                    <input type="hidden" name="action" value="remove">
                    <button type="submit" class="btn btn-danger btn-sm">Remove Friend</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Display Non-Friends -->
<h3>Add Friends</h3>
<?php if (empty($non_friends)): ?>
    <p>No users to add.</p>
<?php else: ?>
    <?php foreach ($non_friends as $user): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($user['username']); ?></h5>
                <form action="handle_friend_request.php" method="POST" style="display: inline;">
                    <input type="hidden" name="receiver_id" value="<?php echo $user['id']; ?>">
                    <input type="hidden" name="action" value="send">
                    <button type="submit" class="btn btn-primary btn-sm">Send Friend Request</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>