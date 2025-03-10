<?php
include 'config/db.php';

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Fetch additional comments (excluding the first 3)
    $stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? AND comments.parent_comment_id IS NULL ORDER BY comments.created_at ASC LIMIT 3, 18446744073709551615");
    $stmt->execute([$post_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($comments)) {
        foreach ($comments as $comment) {
            echo '
            <div class="card mb-2">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">
                        <a href="profile.php?id=' . $comment['user_id'] . '">
                            ' . htmlspecialchars($comment['username']) . '
                        </a>
                        <small>' . $comment['created_at'] . '</small>
                    </h6>
                    <p class="card-text">' . htmlspecialchars($comment['content']) . '</p>
                </div>
            </div>';
        }
    } else {
        echo '<p class="text-muted">No more comments.</p>';
    }
}
?>