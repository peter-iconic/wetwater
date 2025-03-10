<?php
session_start();
include 'config/db.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if a story ID is provided in the URL
if (!isset($_GET['id'])) {
    die("Story ID not specified.");
}

$story_id = $_GET['id'];

// Fetch the story details
$stmt = $pdo->prepare("
    SELECT stories.*, users.username, users.profile_picture
    FROM stories
    JOIN users ON stories.user_id = users.id
    WHERE stories.id = ? AND stories.expires_at > NOW()
");
$stmt->execute([$story_id]);
$story = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$story) {
    die("Story not found or has expired.");
}

// Fetch the next and previous stories for navigation
$stmt = $pdo->prepare("
    SELECT id FROM stories
    WHERE user_id = ? AND expires_at > NOW() AND id > ?
    ORDER BY id ASC
    LIMIT 1
");
$stmt->execute([$story['user_id'], $story_id]);
$next_story = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT id FROM stories
    WHERE user_id = ? AND expires_at > NOW() AND id < ?
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([$story['user_id'], $story_id]);
$prev_story = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Story</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .story-container {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: black;
        }

        .story-media {
            max-width: 100%;
            max-height: 90vh;
            border-radius: 10px;
        }

        .navigation-buttons {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }

        .navigation-buttons button {
            background-color: rgba(0, 0, 0, 0.5);
            border: none;
            color: white;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
        }

        .story-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="story-container">
        <!-- Display the story media -->
        <?php if (strpos($story['media_url'], '.mp4') !== false): ?>
            <video class="story-media" controls autoplay>
                <source src="<?php echo $story['media_url']; ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        <?php else: ?>
            <img src="<?php echo $story['media_url']; ?>" alt="Story" class="story-media">
        <?php endif; ?>

        <!-- Navigation buttons -->
        <div class="navigation-buttons">
            <?php if ($prev_story): ?>
                <a href="view_story.php?id=<?php echo $prev_story['id']; ?>" class="btn btn-secondary">&#10094;</a>
            <?php endif; ?>
            <?php if ($next_story): ?>
                <a href="view_story.php?id=<?php echo $next_story['id']; ?>" class="btn btn-secondary">&#10095;</a>
            <?php endif; ?>
        </div>

        <!-- Story info -->
        <div class="story-info">
            <img src="assets/images/<?php echo htmlspecialchars($story['profile_picture']); ?>" alt="Profile Picture"
                class="rounded-circle" width="30" height="30">
            <span><?php echo htmlspecialchars($story['username']); ?></span>
            <small><?php echo date('h:i A', strtotime($story['created_at'])); ?></small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>