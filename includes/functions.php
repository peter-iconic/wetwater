<?php
function addNotification($pdo, $user_id, $message)
{
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$user_id, $message]);
}
?>
<?php
// includes/functions.php

// Gamification Functions
function updateLoginStreak($user_id)
{
    global $pdo;

    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    try {
        $stmt = $pdo->prepare("SELECT * FROM streaks WHERE user_id = ? AND streak_type = 'login'");
        $stmt->execute([$user_id]);
        $streak = $stmt->fetch();

        if ($streak) {
            if ($streak['last_activity'] == $yesterday) {
                // Continue streak
                $new_streak = $streak['current_streak'] + 1;
                $longest_streak = max($new_streak, $streak['longest_streak']);

                $stmt = $pdo->prepare("UPDATE streaks SET current_streak = ?, longest_streak = ?, last_activity = ? WHERE id = ?");
                $stmt->execute([$new_streak, $longest_streak, $today, $streak['id']]);

                // Award points for streak
                awardPoints($user_id, $new_streak * 10, 'login_streak');
            } elseif ($streak['last_activity'] != $today) {
                // Reset streak
                $stmt = $pdo->prepare("UPDATE streaks SET current_streak = 1, last_activity = ? WHERE id = ?");
                $stmt->execute([$today, $streak['id']]);
            }
        } else {
            // Create new streak
            $stmt = $pdo->prepare("INSERT INTO streaks (user_id, streak_type, current_streak, longest_streak, last_activity) VALUES (?, 'login', 1, 1, ?)");
            $stmt->execute([$user_id, $today]);
        }
    } catch (PDOException $e) {
        error_log("Error updating login streak: " . $e->getMessage());
    }
}

function awardPoints($user_id, $points, $reason)
{
    global $pdo;

    try {
        // Update user points
        $stmt = $pdo->prepare("INSERT INTO user_points (user_id, points) VALUES (?, ?) ON DUPLICATE KEY UPDATE points = points + VALUES(points)");
        $stmt->execute([$user_id, $points]);

        // Check for level up
        checkLevelUp($user_id);

        // Log the points award (if points_log table exists)
        $stmt = $pdo->prepare("INSERT INTO points_log (user_id, points, reason, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $points, $reason]);
    } catch (PDOException $e) {
        error_log("Error awarding points: " . $e->getMessage());
    }
}

function checkLevelUp($user_id)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT points, level FROM user_points WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user) {
            $new_level = floor($user['points'] / 1000) + 1;
            if ($new_level > $user['level']) {
                $stmt = $pdo->prepare("UPDATE user_points SET level = ? WHERE user_id = ?");
                $stmt->execute([$new_level, $user_id]);

                // Award level up bonus
                awardPoints($user_id, $new_level * 50, 'level_up');
            }
        }
    } catch (PDOException $e) {
        error_log("Error checking level up: " . $e->getMessage());
    }
}

// AI Recommendation Functions
function getRecommendedPosts($user_id)
{
    global $pdo;

    try {
        // Simple collaborative filtering based on user behavior
        $stmt = $pdo->prepare("
            SELECT p.*, u.username, u.profile_picture,
                   (SELECT COUNT(*) FROM reactions r WHERE r.post_id = p.id AND r.type = 'like') as like_count,
                   (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) as comment_count
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.user_id IN (
                SELECT DISTINCT p2.user_id 
                FROM posts p2 
                JOIN reactions r ON p2.id = r.post_id 
                WHERE r.user_id = ? 
                AND r.type = 'like'
            )
            AND p.id NOT IN (
                SELECT post_id FROM reactions WHERE user_id = ?
            )
            ORDER BY like_count DESC, comment_count DESC
            LIMIT 5
        ");
        $stmt->execute([$user_id, $user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting recommended posts: " . $e->getMessage());
        return [];
    }
}

function getTrendingTopics()
{
    global $pdo;

    try {
        // First check if post_tags table exists, if not return empty array
        $stmt = $pdo->prepare("
            SELECT tag, COUNT(*) as post_count
            FROM post_tags
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            GROUP BY tag
            ORDER BY post_count DESC
            LIMIT 10
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // If table doesn't exist, return sample data
        return [
            ['tag' => 'socialmedia', 'post_count' => 45],
            ['tag' => 'trending', 'post_count' => 32],
            ['tag' => 'news', 'post_count' => 28],
            ['tag' => 'tech', 'post_count' => 25]
        ];
    }
}

function getUserStats($user_id)
{
    global $pdo;

    // Default stats
    $stats = [
        'points' => 0,
        'level' => 1,
        'login_streak' => 0,
        'post_count' => 0,
        'follower_count' => 0,
        'total_engagement' => 0,
        'engagement_rate' => 0
    ];

    try {
        // Get points and level
        $stmt = $pdo->prepare("SELECT points, level FROM user_points WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $points_data = $stmt->fetch();

        if ($points_data) {
            $stats['points'] = $points_data['points'];
            $stats['level'] = $points_data['level'];
        }

        // Get login streak
        $stmt = $pdo->prepare("SELECT current_streak FROM streaks WHERE user_id = ? AND streak_type = 'login'");
        $stmt->execute([$user_id]);
        $streak_data = $stmt->fetch();
        $stats['login_streak'] = $streak_data ? $streak_data['current_streak'] : 0;

        // Get post count
        $stmt = $pdo->prepare("SELECT COUNT(*) as post_count FROM posts WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $post_data = $stmt->fetch();
        $stats['post_count'] = $post_data['post_count'];

        // Get follower count
        $stmt = $pdo->prepare("SELECT COUNT(*) as follower_count FROM followers WHERE following_id = ?");
        $stmt->execute([$user_id]);
        $follower_data = $stmt->fetch();
        $stats['follower_count'] = $follower_data['follower_count'];

        // Get total engagement
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_engagement FROM reactions WHERE post_id IN (SELECT id FROM posts WHERE user_id = ?)");
        $stmt->execute([$user_id]);
        $engagement_data = $stmt->fetch();
        $stats['total_engagement'] = $engagement_data['total_engagement'];

        // Calculate engagement rate
        $stats['engagement_rate'] = $stats['post_count'] > 0 ?
            round(($stats['total_engagement'] / $stats['post_count']) * 100, 1) : 0;

    } catch (PDOException $e) {
        error_log("Error getting user stats: " . $e->getMessage());
    }

    return $stats;
}

function getDailyChallenges($user_id)
{
    // Sample challenges - you can make these dynamic later
    return [
        [
            'title' => 'Post 3 times today',
            'progress' => 60,
            'reward_points' => 50
        ],
        [
            'title' => 'Like 10 posts',
            'progress' => 80,
            'reward_points' => 30
        ],
        [
            'title' => 'Comment on 5 posts',
            'progress' => 40,
            'reward_points' => 40
        ]
    ];
}

function getFriendSuggestions($user_id)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.profile_picture,
                   (SELECT COUNT(*) FROM friends f1 
                    WHERE f1.user_id = u.id AND f1.friend_id IN (
                        SELECT friend_id FROM friends WHERE user_id = ?
                    )) as mutual_friends
            FROM users u
            WHERE u.id != ? 
            AND u.id NOT IN (
                SELECT friend_id FROM friends WHERE user_id = ?
                UNION
                SELECT receiver_id FROM friend_requests WHERE sender_id = ?
            )
            ORDER BY mutual_friends DESC
            LIMIT 5
        ");
        $stmt->execute([$user_id, $user_id, $user_id, $user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting friend suggestions: " . $e->getMessage());
        return [];
    }
}

function getPostsWithEngagement($user_id, $limit = 10, $offset = 0)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            SELECT 
                posts.*, 
                users.username,
                users.profile_picture,
                (SELECT COUNT(*) FROM posts AS reposts WHERE reposts.original_post_id = posts.id) AS repost_count,
                (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count,
                (SELECT COUNT(*) FROM reactions WHERE reactions.post_id = posts.id AND type = 'like') AS like_count,
                (SELECT COUNT(*) FROM reactions WHERE reactions.post_id = posts.id AND type = 'dislike') AS dislike_count,
                (SELECT type FROM reactions WHERE reactions.post_id = posts.id AND reactions.user_id = ?) AS user_reaction
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            ORDER BY posts.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user_id, $limit, $offset]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting posts: " . $e->getMessage());
        return [];
    }
}

function time_ago($datetime)
{
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . 'm ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . 'h ago';
    } else {
        return floor($diff / 86400) . 'd ago';
    }
}
?>