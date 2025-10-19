<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

// Check if user ID is provided and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$user_id = intval($_GET['id']);
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch user details with error handling
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found.");
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Fetch user's posts
try {
    $stmt = $pdo->prepare("
        SELECT 
            posts.*,
            users.username,
            users.profile_picture,
            (SELECT COUNT(*) FROM reactions WHERE reactions.post_id = posts.id AND type = 'like') AS like_count,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count,
            (SELECT COUNT(*) FROM posts AS reposts WHERE reposts.original_post_id = posts.id) AS repost_count
        FROM posts 
        JOIN users ON posts.user_id = users.id
        WHERE posts.user_id = ? 
        ORDER BY posts.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $posts = [];
    error_log("Error fetching posts: " . $e->getMessage());
}

// Fetch followers/following counts with error handling
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS followers_count FROM followers WHERE following_id = ?");
    $stmt->execute([$user_id]);
    $followers_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $followers_count = $followers_result ? $followers_result['followers_count'] : 0;

    $stmt = $pdo->prepare("SELECT COUNT(*) AS following_count FROM followers WHERE follower_id = ?");
    $stmt->execute([$user_id]);
    $following_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $following_count = $following_result ? $following_result['following_count'] : 0;

} catch (PDOException $e) {
    $followers_count = 0;
    $following_count = 0;
    error_log("Error fetching follower counts: " . $e->getMessage());
}

// Check if current user is following this profile
$is_following = false;
if ($current_user_id && $current_user_id != $user_id) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM followers WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$current_user_id, $user_id]);
        $is_following = $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    } catch (PDOException $e) {
        error_log("Error checking follow status: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username'] ?? 'User'); ?>'s Profile - WetWater</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            min-height: 100vh;
            color: #2d3748;
        }

        .profile-container {
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 20px;
        }

        .profile-header {
            background: linear-gradient(135deg, #6f42c1, #a855f7);
            color: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(111, 66, 193, 0.15);
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            margin-bottom: 20px;
            object-fit: cover;
            background: #fff;
        }

        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 20px 0;
        }

        .stat-item {
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .stat-item:hover {
            transform: translateY(-2px);
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            display: block;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .post-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            transition: transform 0.2s ease;
        }

        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .post-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .post-actions {
            display: flex;
            justify-content: space-around;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .action-btn {
            background: none;
            border: none;
            color: #718096;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .action-btn:hover {
            background: rgba(111, 66, 193, 0.1);
            color: #6f42c1;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6f42c1, #a855f7);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #6f42c1;
            color: #6f42c1;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: #6f42c1;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #a855f7;
        }

        .user-details {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.9);
        }

        @media (max-width: 768px) {
            .profile-container {
                margin-top: 70px;
                padding: 10px;
            }

            .profile-header {
                padding: 30px 20px;
            }

            .profile-stats {
                gap: 20px;
            }

            .posts-grid {
                grid-template-columns: 1fr;
            }

            .user-details {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'default_profile.jpg'); ?>"
                class="profile-avatar" alt="<?php echo htmlspecialchars($user['username'] ?? 'User'); ?>"
                onerror="this.src='default_profile.jpg'">

            <h1><?php echo htmlspecialchars($user['username'] ?? 'User'); ?></h1>
            <p class="mb-3"><?php echo htmlspecialchars($user['email'] ?? 'No email provided'); ?></p>

            <?php if (!empty($user['bio'])): ?>
                <p class="mb-4"><?php echo htmlspecialchars($user['bio']); ?></p>
            <?php endif; ?>

            <div class="profile-stats">
                <div class="stat-item" data-bs-toggle="modal" data-bs-target="#followersModal">
                    <span class="stat-number"><?php echo $followers_count; ?></span>
                    <span class="stat-label">Followers</span>
                </div>
                <div class="stat-item" data-bs-toggle="modal" data-bs-target="#followingModal">
                    <span class="stat-number"><?php echo $following_count; ?></span>
                    <span class="stat-label">Following</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo count($posts); ?></span>
                    <span class="stat-label">Posts</span>
                </div>
            </div>

            <div class="user-details">
                <?php if (!empty($user['location'])): ?>
                    <div class="detail-item">
                        <i class="bi bi-geo-alt"></i>
                        <span><?php echo htmlspecialchars($user['location']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['website'])): ?>
                    <div class="detail-item">
                        <i class="bi bi-link-45deg"></i>
                        <a href="<?php echo htmlspecialchars($user['website']); ?>" target="_blank"
                            style="color: inherit; text-decoration: none;">
                            Website
                        </a>
                    </div>
                <?php endif; ?>

                <div class="detail-item">
                    <i class="bi bi-calendar"></i>
                    <span>Joined <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                </div>
            </div>

            <div class="mt-4">
                <?php if ($current_user_id && $current_user_id == $user_id): ?>
                    <a href="edit_profile.php" class="btn-primary">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                    <a href="logout.php" class="btn-outline" style="border-color: #ef4444; color: #ef4444;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                <?php elseif ($current_user_id && $current_user_id != $user_id): ?>
                    <button class="btn-primary" id="followBtn" data-user-id="<?php echo $user_id; ?>">
                        <i class="bi bi-<?php echo $is_following ? 'check' : 'plus'; ?>"></i>
                        <?php echo $is_following ? 'Following' : 'Follow'; ?>
                    </button>
                    <button class="btn-outline">
                        <i class="bi bi-chat"></i> Message
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Posts Section -->
        <div>
            <h2 class="mb-4">Posts</h2>

            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <i class="bi bi-file-earmark-text"></i>
                    <h3>No posts yet</h3>
                    <p>
                        <?php echo ($current_user_id == $user_id) ? 'Share your first post with the community!' : 'This user hasn\'t posted anything yet.'; ?>
                    </p>
                    <?php if ($current_user_id == $user_id): ?>
                        <a href="create_post.php" class="btn-primary mt-3">
                            <i class="bi bi-plus-circle"></i> Create First Post
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="posts-grid">
                    <?php foreach ($posts as $post): ?>
                        <div class="post-card">
                            <?php if (!empty($post['image'])): ?>
                                <img src="<?php echo htmlspecialchars($post['image']); ?>" class="post-image" alt="Post image"
                                    onerror="this.style.display='none'">
                            <?php endif; ?>

                            <p class="post-content"><?php echo htmlspecialchars($post['content']); ?></p>

                            <?php if (!empty($post['caption'])): ?>
                                <p class="text-muted small"><?php echo htmlspecialchars($post['caption']); ?></p>
                            <?php endif; ?>

                            <div class="post-actions">
                                <button class="action-btn like-btn" data-post-id="<?php echo $post['id']; ?>">
                                    <i class="bi bi-heart"></i>
                                    <span class="like-count"><?php echo $post['like_count']; ?></span>
                                </button>
                                <button class="action-btn comment-btn" data-post-id="<?php echo $post['id']; ?>">
                                    <i class="bi bi-chat"></i>
                                    <span class="comment-count"><?php echo $post['comment_count']; ?></span>
                                </button>
                                <button class="action-btn repost-btn" data-post-id="<?php echo $post['id']; ?>">
                                    <i class="bi bi-arrow-repeat"></i>
                                    <span class="repost-count"><?php echo $post['repost_count']; ?></span>
                                </button>
                            </div>

                            <small class="text-muted">
                                <?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Followers Modal -->
    <div class="modal fade" id="followersModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Followers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php
                    try {
                        $stmt = $pdo->prepare("
                            SELECT users.id, users.username, users.profile_picture 
                            FROM followers 
                            JOIN users ON followers.follower_id = users.id 
                            WHERE following_id = ?
                        ");
                        $stmt->execute([$user_id]);
                        $followers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $followers = [];
                    }
                    ?>
                    <?php if (empty($followers)): ?>
                        <div class="empty-state py-4">
                            <i class="bi bi-people"></i>
                            <p class="text-muted mb-0">No followers yet</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($followers as $follower): ?>
                                <a href="profile.php?id=<?php echo $follower['id']; ?>"
                                    class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($follower['profile_picture']); ?>"
                                            class="rounded-circle me-3" width="40" height="40"
                                            onerror="this.src='default_profile.jpg'">
                                        <span><?php echo htmlspecialchars($follower['username']); ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Following Modal -->
    <div class="modal fade" id="followingModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Following</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php
                    try {
                        $stmt = $pdo->prepare("
                            SELECT users.id, users.username, users.profile_picture 
                            FROM followers 
                            JOIN users ON followers.following_id = users.id 
                            WHERE follower_id = ?
                        ");
                        $stmt->execute([$user_id]);
                        $following = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        $following = [];
                    }
                    ?>
                    <?php if (empty($following)): ?>
                        <div class="empty-state py-4">
                            <i class="bi bi-people"></i>
                            <p class="text-muted mb-0">Not following anyone yet</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($following as $followed_user): ?>
                                <a href="profile.php?id=<?php echo $followed_user['id']; ?>"
                                    class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($followed_user['profile_picture']); ?>"
                                            class="rounded-circle me-3" width="40" height="40"
                                            onerror="this.src='default_profile.jpg'">
                                        <span><?php echo htmlspecialchars($followed_user['username']); ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Follow/Unfollow functionality
        $(document).ready(function () {
            $('#followBtn').on('click', function () {
                const userId = $(this).data('user-id');
                const isFollowing = $(this).text().trim() === 'Following';

                $.post('follow_handler.php', {
                    user_id: userId,
                    action: isFollowing ? 'unfollow' : 'follow'
                }, function (response) {
                    if (response.success) {
                        const $btn = $('#followBtn');
                        if (isFollowing) {
                            $btn.html('<i class="bi bi-plus"></i> Follow');
                            $btn.removeClass('btn-following');
                        } else {
                            $btn.html('<i class="bi bi-check"></i> Following');
                            $btn.addClass('btn-following');
                        }
                        // Update follower count
                        $('.stat-item:first-child .stat-number').text(response.followers_count);
                    }
                }, 'json');
            });

            // Like functionality
            $('.like-btn').on('click', function () {
                const postId = $(this).data('post-id');
                const $btn = $(this);

                $.post('react.php', {
                    post_id: postId,
                    type: 'like',
                    ajax: true
                }, function (response) {
                    if (response.success) {
                        $btn.find('.like-count').text(response.likes);
                        if (response.action === 'added') {
                            $btn.addClass('liked');
                            $btn.find('i').removeClass('bi-heart').addClass('bi-heart-fill');
                        } else {
                            $btn.removeClass('liked');
                            $btn.find('i').removeClass('bi-heart-fill').addClass('bi-heart');
                        }
                    }
                }, 'json');
            });
        });
    </script>
</body>

</html>
<?php include 'includes/footer.php'; ?>