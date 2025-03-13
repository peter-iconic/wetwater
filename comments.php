<?php
include 'config/db.php';

$videoId = $_GET['video_id'] ?? null;

if (!$videoId) {
    die("Video ID is required.");
}

// Fetch video details
try {
    $stmt = $pdo->prepare("
        SELECT videos.*, users.username 
        FROM videos 
        JOIN users ON videos.user_id = users.id 
        WHERE videos.id = ?
    ");
    $stmt->execute([$videoId]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$video) {
        die("Video not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Fetch comments for the video
try {
    $stmt = $pdo->prepare("
        SELECT comments.*, users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.post_id = ? AND comments.type = 'video' 
        ORDER BY comments.created_at DESC
    ");
    $stmt->execute([$videoId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2>Comments for: <?php echo htmlspecialchars($video['title']); ?></h2>
        <div class="mt-4">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($comment['username']); ?></h6>
                            <p class="card-text"><?php echo htmlspecialchars($comment['content']); ?></p>
                            <small class="text-muted"><?php echo $comment['created_at']; ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">No comments yet.</div>
            <?php endif; ?>
        </div>

        <!-- Add Comment Form -->
        <div class="mt-4">
            <h4>Add a Comment</h4>
            <form action="add_comment.php" method="POST">
                <input type="hidden" name="video_id" value="<?php echo $videoId; ?>">
                <input type="hidden" name="type" value="video">
                <div class="form-group">
                    <textarea name="content" class="form-control" rows="3" placeholder="Write your comment..."
                        required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Post Comment</button>
            </form>
        </div>
    </div>
</body>

</html>