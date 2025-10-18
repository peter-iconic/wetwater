<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch notifications with sender info and post info
$stmt = $pdo->prepare("
    SELECT n.*, 
           u.username AS sender_name, 
           u.profile_picture AS sender_pic,
           p.id AS post_id,
           p.content AS post_content
    FROM notifications n
    LEFT JOIN users u ON n.sender_id = u.id
    LEFT JOIN posts p ON n.post_id = p.id
    WHERE n.user_id = ?
    ORDER BY n.created_at DESC
");
$stmt->execute([$userId]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4 mb-5" style="padding-top: 60px; padding-bottom: 100px;">
    <h2 class="text-center mb-4">Notifications</h2>

    <div id="notification-feed">
        <?php if (empty($notifications)): ?>
            <p class="text-center text-muted">No new notifications.</p>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <div class="card mb-3 shadow-sm" style="border-radius: 10px;">
                    <div class="card-body d-flex align-items-center">
                        <!-- Profile Picture -->
                        <a href="profile.php?id=<?php echo htmlspecialchars($n['sender_id']); ?>" class="me-3">
                            <img src="<?php echo !empty($n['sender_pic']) ? htmlspecialchars($n['sender_pic']) : 'assets/default-avatar.png'; ?>"
                                 alt="Profile"
                                 class="rounded-circle"
                                 width="50" height="50"
                                 style="object-fit: cover;">
                        </a>

                        <!-- Notification Message -->
                        <div class="flex-grow-1">
                            <a href="profile.php?id=<?php echo htmlspecialchars($n['sender_id']); ?>"
                               class="fw-bold text-decoration-none text-dark">
                                <?php echo htmlspecialchars($n['sender_name'] ?? 'Unknown User'); ?>
                            </a>
                            <span class="text-muted">
                                <?php echo htmlspecialchars($n['message']); ?>
                            </span>
                            <?php if (!empty($n['post_id'])): ?>
                                <a href="view_post.php?id=<?php echo htmlspecialchars($n['post_id']); ?>"
                                   class="text-primary text-decoration-none">View Post</a>
                            <?php endif; ?>
                            <div>
                                <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($n['created_at'])); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // WebSocket connection for real-time notifications
    const socket = new WebSocket('ws://localhost:8080');

    socket.onopen = () => {
        console.log('Connected to WebSocket');
        socket.send(JSON.stringify({
            type: 'register',
            userId: <?php echo $_SESSION['user_id']; ?>
        }));
    };

    socket.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.type === 'notification') {
            $('#notification-feed').prepend(`
                <div class="card mb-3 shadow-sm" style="border-radius: 10px;">
                    <div class="card-body d-flex align-items-center">
                        <a href="profile.php?id=${data.sender_id}" class="me-3">
                            <img src="${data.sender_pic || 'assets/default-avatar.png'}"
                                 alt="Profile"
                                 class="rounded-circle"
                                 width="50" height="50"
                                 style="object-fit: cover;">
                        </a>
                        <div class="flex-grow-1">
                            <a href="profile.php?id=${data.sender_id}"
                               class="fw-bold text-decoration-none text-dark">
                                ${data.sender_name}
                            </a>
                            <span class="text-muted">${data.message}</span>
                            ${data.post_id ? `<a href="view_post.php?id=${data.post_id}" class="text-primary text-decoration-none">View Post</a>` : ''}
                            <div><small class="text-muted">${new Date().toLocaleString()}</small></div>
                        </div>
                    </div>
                </div>
            `);
        }
    };

    socket.onerror = (error) => console.error('WebSocket error:', error);
    socket.onclose = () => console.log('WebSocket closed');
</script>

<?php include 'includes/footer.php'; ?>
