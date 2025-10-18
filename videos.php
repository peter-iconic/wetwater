<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

try {
    $stmt = $pdo->prepare("
        SELECT 
            videos.*, 
            users.username,
            (SELECT COUNT(*) FROM reactions WHERE post_id = videos.id AND type = 'like') AS likes,
            (SELECT COUNT(*) FROM reactions WHERE post_id = videos.id AND type = 'dislike') AS dislikes,
            (SELECT COUNT(*) FROM views WHERE video_id = videos.id) AS views,
            (SELECT COUNT(*) FROM comments WHERE post_id = videos.id AND type = 'video') AS comments,
            videos.shares
        FROM videos
        JOIN users ON videos.user_id = users.id
        ORDER BY videos.created_at DESC
    ");
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reels - For You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background-color: black;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        .reels-container {
            height: 100vh;
            overflow-y: auto;
            scroll-snap-type: y mandatory;
            padding-bottom: 80px;
            /* footer padding */
        }

        .reel {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            scroll-snap-align: start;
        }

        video {
            width: 100%;
            max-height: 100vh;
        }

        .top-buttons {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
            z-index: 100;
        }

        .top-buttons button {
            background: rgba(0, 0, 0, 0.5);
            border: none;
            color: white;
            font-weight: bold;
            padding: 5px 15px;
            border-radius: 20px;
            cursor: pointer;
        }

        .top-buttons button.active {
            background: white;
            color: black;
        }

        .video-overlay {
            position: absolute;
            bottom: 80px;
            left: 20px;
            color: white;
        }

        .action-buttons {
            position: absolute;
            bottom: 80px;
            right: 20px;
            text-align: center;
        }

        .action-buttons i {
            font-size: 24px;
            margin-bottom: 20px;
            display: block;
            cursor: pointer;
        }

        .sound-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            cursor: pointer;
        }

        .share-button {
            position: absolute;
            top: 60px;
            right: 20px;
            color: white;
            cursor: pointer;
        }

        .comments-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .comments-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close-comments {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="reels-container" id="reelsContainer">
        <?php foreach ($videos as $video): ?>
            <div class="reel" data-video-id="<?php echo $video['id']; ?>">

                <!-- Top Buttons Overlay -->
                <div class="top-buttons">
                    <button class="active" onclick="filterFeed(this)">For You</button>
                    <button onclick="filterFeed(this)">Following</button>
                    <button onclick="filterFeed(this)">Trending</button>
                </div>

                <video class="reel-video" muted loop>
                    <source src="<?php echo htmlspecialchars($video['video_url']); ?>" type="video/mp4">
                </video>

                <div class="sound-toggle" onclick="toggleSound(this)">
                    <i class="bi bi-volume-mute"></i>
                </div>

                <div class="share-button"
                    onclick="shareVideo(<?php echo $video['id']; ?>,'<?php echo htmlspecialchars($video['video_url']); ?>')">
                    <i class="bi bi-share"></i> <span class="share-count"><?php echo $video['shares']; ?></span>
                </div>

                <div class="video-overlay">
                    <h5><?php echo htmlspecialchars($video['title']); ?></h5>
                    <p><?php echo htmlspecialchars($video['description']); ?></p>
                    <small>Posted by: <?php echo htmlspecialchars($video['username']); ?></small>
                </div>

                <div class="action-buttons">
                    <i class="bi bi-hand-thumbs-up text-success" onclick="reactToVideo(<?php echo $video['id']; ?>,'like')">
                        <span class="like-count"><?php echo $video['likes']; ?></span>
                    </i>
                    <i class="bi bi-hand-thumbs-down text-danger"
                        onclick="reactToVideo(<?php echo $video['id']; ?>,'dislike')">
                        <span class="dislike-count"><?php echo $video['dislikes']; ?></span>
                    </i>
                    <i class="bi bi-eye text-primary"><span class="view-count"><?php echo $video['views']; ?></span></i>
                    <i class="bi bi-chat text-light" onclick="showComments(<?php echo $video['id']; ?>)">
                        <span class="comment-count"><?php echo $video['comments']; ?></span>
                    </i>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="comments-overlay" id="commentsOverlay">
        <div class="comments-container" id="commentsContainer"></div>
        <div class="close-comments" onclick="closeComments()">&times;</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const videos = document.querySelectorAll(".reel-video");
            const container = document.querySelector(".reels-container");

            function playVisibleVideo() {
                videos.forEach(video => {
                    const rect = video.getBoundingClientRect();
                    rect.top >= 0 && rect.bottom <= window.innerHeight ? video.play() : video.pause();
                });
            }

            videos.forEach(video => {
                video.addEventListener("click", () => video.paused ? video.play() : video.pause());
            });

            container.addEventListener("scroll", playVisibleVideo);
            playVisibleVideo();
        });

        function toggleSound(button) {
            const video = button.closest(".reel").querySelector(".reel-video");
            if (video.muted) { video.muted = false; button.innerHTML = '<i class="bi bi-volume-up"></i>'; }
            else { video.muted = true; button.innerHTML = '<i class="bi bi-volume-mute"></i>'; }
        }

        function shareVideo(videoId, videoUrl) {
            if (navigator.share) { navigator.share({ title: 'Check this video!', url: videoUrl }); }
            else alert('Sharing not supported.');
        }

        function reactToVideo(videoId, type) {
            fetch('react.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ video_id: videoId, type: type }) })
                .then(res => res.json()).then(data => {
                    if (data.success) {
                        const reel = document.querySelector(`.reel[data-video-id="${videoId}"]`);
                        reel.querySelector('.like-count').innerText = data.likes;
                        reel.querySelector('.dislike-count').innerText = data.dislikes;
                    }
                });
        }

        function showComments(videoId) {
            fetch(`fetch_comments.php?video_id=${videoId}`).then(res => res.text()).then(html => {
                document.getElementById('commentsContainer').innerHTML = html;
                document.getElementById('commentsOverlay').style.display = 'flex';
            });
        }

        function closeComments() { document.getElementById('commentsOverlay').style.display = 'none'; }

        document.getElementById('commentsOverlay').addEventListener('click', (e) => {
            if (e.target === document.getElementById('commentsOverlay')) closeComments();
        });

        // Handle top buttons switching
        function filterFeed(button) {
            const buttons = button.parentElement.querySelectorAll('button');
            buttons.forEach(b => b.classList.remove('active'));
            button.classList.add('active');
            alert('Filter: ' + button.innerText + ' (AJAX fetch can be implemented)');
        }
    </script>
</body>

</html>
<?php require 'includes/footer.php'; ?>