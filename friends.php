<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the users the logged-in user is following
$stmt = $pdo->prepare("
    SELECT users.id, users.username, users.profile_picture, users.bio
    FROM followers
    JOIN users ON followers.following_id = users.id
    WHERE followers.follower_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$following = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the users who are following the logged-in user
$stmt = $pdo->prepare("
    SELECT users.id, users.username, users.profile_picture, users.bio
    FROM followers
    JOIN users ON followers.follower_id = users.id
    WHERE followers.following_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$followers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch users who are not being followed by the logged-in user
$stmt = $pdo->prepare("
    SELECT users.id, users.username, users.profile_picture, users.bio
    FROM users
    WHERE users.id != ?
    AND users.id NOT IN (
        SELECT following_id FROM followers WHERE follower_id = ?
    )
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$non_following = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container" style="padding-top: 10px; padding-bottom: 80px;">
    <h1 class="text-center mb-4">Your Network</h1>
    <div class="row">
        <!-- Followers Column -->
        <div class="col-md-4">
            <h3>Followers</h3>
            <?php if (empty($followers)): ?>
                <p>No followers yet.</p>
            <?php else: ?>
                <?php foreach ($followers as $follower): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <img src="assets/images/<?php echo htmlspecialchars($follower['profile_picture']); ?>"
                                alt="Profile Picture" class="rounded-circle" width="50" height="50">
                            <h5 class="card-title mt-2">
                                <a href="profile.php?id=<?php echo $follower['id']; ?>">
                                    <?php echo htmlspecialchars($follower['username']); ?>
                                </a>
                            </h5>
                            <p class="card-text"><?php echo htmlspecialchars($follower['bio']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Following Column -->
        <div class="col-md-4">
            <h3>Following</h3>
            <?php if (empty($following)): ?>
                <p>You are not following anyone.</p>
            <?php else: ?>
                <?php foreach ($following as $user): ?>
                    <div class="card mb-3" id="following-<?php echo $user['id']; ?>">
                        <div class="card-body">
                            <img src="assets/images/<?php echo htmlspecialchars($user['profile_picture']); ?>"
                                alt="Profile Picture" class="rounded-circle" width="50" height="50">
                            <h5 class="card-title mt-2">
                                <a href="profile.php?id=<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </a>
                            </h5>
                            <p class="card-text"><?php echo htmlspecialchars($user['bio']); ?></p>
                            <button class="btn btn-danger btn-sm unfollow-button"
                                data-user-id="<?php echo $user['id']; ?>">Unfollow</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Suggested Users Column -->
        <div class="col-md-4">
            <h3>Suggested Users</h3>
            <?php if (empty($non_following)): ?>
                <p>No users to follow.</p>
            <?php else: ?>
                <?php foreach ($non_following as $user): ?>
                    <div class="card mb-3" id="user-<?php echo $user['id']; ?>">
                        <div class="card-body">
                            <img src="assets/images/<?php echo htmlspecialchars($user['profile_picture']); ?>"
                                alt="Profile Picture" class="rounded-circle" width="50" height="50">
                            <h5 class="card-title mt-2"><?php echo htmlspecialchars($user['username']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($user['bio']); ?></p>
                            <button class="btn btn-primary btn-sm follow-button"
                                data-user-id="<?php echo $user['id']; ?>">Follow</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Follow a user
    $(document).on('click', '.follow-button', function () {
        const userId = $(this).data('user-id');
        const button = $(this);

        $.ajax({
            url: 'handle_follow.php',
            method: 'POST',
            data: { following_id: userId, action: 'follow' },
            success: function (response) {
                // Move the user card to the "Following" section
                const userCard = $('#user-' + userId);
                userCard.find('.follow-button').remove();
                userCard.append('<button class="btn btn-danger btn-sm unfollow-button" data-user-id="' + userId + '">Unfollow</button>');
                $('#following-list').append(userCard);
                alert('Followed successfully.');
            },
            error: function (xhr, status, error) {
                console.error('Error following user:', error);
                alert('Failed to follow user.');
            }
        });
    });

    // Unfollow a user
    $(document).on('click', '.unfollow-button', function () {
        const userId = $(this).data('user-id');
        const button = $(this);

        $.ajax({
            url: 'handle_follow.php',
            method: 'POST',
            data: { following_id: userId, action: 'unfollow' },
            success: function (response) {
                // Move the user card to the "Non-Following" section
                const userCard = $('#following-' + userId);
                userCard.find('.unfollow-button').remove();
                userCard.append('<button class="btn btn-primary btn-sm follow-button" data-user-id="' + userId + '">Follow</button>');
                $('#non-following-list').append(userCard);
                alert('Unfollowed successfully.');
            },
            error: function (xhr, status, error) {
                console.error('Error unfollowing user:', error);
                alert('Failed to unfollow user.');
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>