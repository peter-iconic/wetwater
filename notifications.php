<?php
session_start();
require_once 'config/db.php';
require_once 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch notifications
try {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching notifications: " . $e->getMessage());
}
?>

<div class="container mt-5" style="padding-bottom: 100px;">
    <h2 class="text-center mb-4">Notifications</h2>

    <div id="notification-feed">
        <?php if (empty($notifications)): ?>
            <p class="text-center">No new notifications.</p>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <?php
                $message = htmlspecialchars($notification['message']);
                $created_at = htmlspecialchars($notification['created_at']);

                // Detect possible user and post IDs in the message
                preg_match('/user:(\d+)/', $notification['message'], $userMatch);
                preg_match('/post:(\d+)/', $notification['message'], $postMatch);

                $actor_id = $userMatch[1] ?? null;
                $post_id = $postMatch[1] ?? null;

                // Fetch actor info if available
                $actor = null;
                if ($actor_id) {
                    $userStmt = $pdo->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
                    $userStmt->execute([$actor_id]);
                    $actor = $userStmt->fetch(PDO::FETCH_ASSOC);
                }

                // Convert @username mentions in message into clickable links
                $message = preg_replace_callback('/@([A-Za-z0-9_]+)/', function ($matches) use ($pdo) {
                    $username = $matches[1];
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user) {
                        return '<a href="profile.php?id=' . $user['id'] . '" style="color:#6c63ff;text-decoration:none;">@' . htmlspecialchars($username) . '</a>';
                    }
                    return '@' . htmlspecialchars($username);
                }, $notification['message']);
                ?>

                <!-- Notification Card -->
                <div class="card mb-3 shadow-sm notification-card" data-post="<?php echo $post_id; ?>"
                    style="border-radius: 12px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;">
                    <div class="card-body d-flex align-items-center">
                        <!-- Profile Picture -->
                        <a href="<?php echo $actor_id ? 'profile.php?id=' . $actor_id : '#'; ?>"
                            onclick="event.stopPropagation();">
                            <img src="<?php echo htmlspecialchars($actor['profile_picture'] ?? 'assets\images\default_profile.jpg'); ?>"
                                class="rounded-circle me-3" alt="User" style="width: 50px; height: 50px; object-fit: cover;">
                        </a>

                        <div>
                            <!-- Username -->
                            <?php if ($actor): ?>
                                <a href="profile.php?id=<?php echo $actor_id; ?>" onclick="event.stopPropagation();"
                                    style="font-weight: bold; color: #333; text-decoration: none;">
                                    <?php echo htmlspecialchars($actor['username']); ?>
                                </a>
                            <?php endif; ?>
                            <!-- Message -->
                            <p class="mb-1"><?php echo $message; ?></p>
                            <small class="text-muted"><?php echo $created_at; ?></small>
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
    $(document).ready(function () {
        // Open post when clicking the card
        $(document).on('click', '.notification-card', function (e) {
            const postId = $(this).data('post');
            if (postId) {
                window.location.href = 'post.php?id=' + postId;
            }
        });

        // Smooth hover animation
        $(document).on('mouseenter', '.notification-card', function () {
            $(this).css({ 'transform': 'scale(1.02)', 'box-shadow': '0 4px 15px rgba(0,0,0,0.1)' });
        }).on('mouseleave', '.notification-card', function () {
            $(this).css({ 'transform': 'scale(1)', 'box-shadow': 'none' });
        });
    });

    // WebSocket for real-time notifications
    const socket = new WebSocket('ws://localhost:8080');

    socket.onopen = () => {
        socket.send(JSON.stringify({
            type: 'register',
            userId: <?php echo $_SESSION['user_id']; ?>
        }));
    };

    socket.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.type === 'notification') {
            const cardHtml = `
            <div class="card mb-3 shadow-sm notification-card" data-post="${data.post_id || ''}" style="border-radius: 12px; cursor: pointer; display:none;">
                <div class="card-body d-flex align-items-center">
                    <a href="profile.php?id=${data.actor_id}" onclick="event.stopPropagation();">
                        <img src="${data.actor_pic || 'assets/default-avatar.png'}"
                             class="rounded-circle me-3"
                             style="width: 50px; height: 50px; object-fit: cover;">
                    </a>
                    <div>
                        <a href="profile.php?id=${data.actor_id}" onclick="event.stopPropagation();" 
                           style="font-weight: bold; color: #333; text-decoration: none;">
                           ${data.actor_name}
                        </a>
                        <p class="mb-1">${data.message}</p>
                        <small class="text-muted">${new Date().toLocaleString()}</small>
                    </div>
                </div>
            </div>`;
            $('#notification-feed').prepend(cardHtml);
            $('#notification-feed .notification-card:first').fadeIn(500);
        }
    };
</script>

<?php include 'includes/footer.php'; ?>