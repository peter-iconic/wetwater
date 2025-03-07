<?php
include 'config/db.php';

// Fetch all posts from the database
$stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display posts
if (empty($posts)) {
    echo '<p class="text-center">No posts yet. Be the first to share!</p>';
} else {
    foreach ($posts as $post) {
        echo '
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <a href="profile.php?id=' . $post['user_id'] . '">
                        ' . htmlspecialchars($post['username']) . '
                    </a>
                </h5>
                <p class="card-text">' . htmlspecialchars($post['content']) . '</p>
                <small class="text-muted">Posted on: ' . $post['created_at'] . '</small>
            </div>
        </div>';
    }
}
?>