<?php
// Start the session
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'config/db.php';

// Fetch videos from the database
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

// Increment view count for each video
foreach ($videos as &$video) {
    try {
        $stmt = $pdo->prepare("INSERT INTO views (video_id, user_id) VALUES (?, ?)");
        $stmt->execute([$video['id'], $_SESSION['user_id']]); // Use the logged-in user's ID
    } catch (PDOException $e) {
        // Handle error (e.g., log it)
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background-color: black;
            overflow: hidden;
        }

        .reels-container {
            height: 100vh;
            overflow-y: auto;
            scroll-snap-type: y mandatory;
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

        /* Comments Overlay */
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
    <div class="reels-container">
        <?php foreach ($videos as $video): ?>
            <div class="reel">
                <!-- Video Player -->
                <video class="reel-video" muted loop>
                    <source src="<?php echo htmlspecialchars($video['video_url']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>

                <!-- Sound Toggle Button -->
                <div class="sound-toggle" onclick="toggleSound(this)">
                    <i class="bi bi-volume-mute"></i>
                </div>

                <!-- Share Button -->
                <div class="share-button"
                    onclick="shareVideo(<?php echo $video['id']; ?>, '<?php echo htmlspecialchars($video['video_url']); ?>')">
                    <i class="bi bi-share"></i> <?php echo $video['shares']; ?>
                </div>

                <!-- Video Overlay (Title, Description, Posted By) -->
                <div class="video-overlay">
                    <h5><?php echo htmlspecialchars($video['title']); ?></h5>
                    <p><?php echo htmlspecialchars($video['description']); ?></p>
                    <small>Posted by: <?php echo htmlspecialchars($video['username']); ?></small>
                </div>

                <!-- Action Buttons (Likes, Dislikes, Views, Comments) -->
                <div class="action-buttons">
                    <i class="bi bi-hand-thumbs-up text-success"
                        onclick="reactToVideo(<?php echo $video['id']; ?>, 'like')"> <?php echo $video['likes']; ?></i>
                    <i class="bi bi-hand-thumbs-down text-danger"
                        onclick="reactToVideo(<?php echo $video['id']; ?>, 'dislike')">
                        <?php echo $video['dislikes']; ?></i>
                    <i class="bi bi-eye text-primary"> <?php echo $video['views']; ?></i>
                    <i class="bi bi-chat text-light" onclick="showComments(<?php echo $video['id']; ?>)">
                        <?php echo $video['comments']; ?></i>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Comments Overlay -->
    <div class="comments-overlay" id="commentsOverlay">
        <div class="comments-container" id="commentsContainer">
            <!-- Comments will be loaded here -->
        </div>
        <div class="close-comments" onclick="closeComments()">&times;</div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const videos = document.querySelectorAll(".reel-video");
            const container = document.querySelector(".reels-container");

            // Play visible video on scroll
            function playVisibleVideo() {
                videos.forEach(video => {
                    const rect = video.getBoundingClientRect();
                    if (rect.top >= 0 && rect.bottom <= window.innerHeight) {
                        video.play();
                    } else {
                        video.pause();
                    }
                });
            }

            // Pause/Play on tap
            videos.forEach(video => {
                video.addEventListener("click", () => {
                    if (video.paused) {
                        video.play();
                    } else {
                        video.pause();
                    }
                });
            });

            // Scroll event listener
            container.addEventListener("scroll", playVisibleVideo);
            playVisibleVideo(); // Initial check
        });

        // Toggle sound
        function toggleSound(button) {
            const video = button.closest(".reel").querySelector(".reel-video");
            if (video.muted) {
                video.muted = false;
                button.innerHTML = '<i class="bi bi-volume-up"></i>';
            } else {
                video.muted = true;
                button.innerHTML = '<i class="bi bi-volume-mute"></i>';
            }
        }

        // Share video
        function shareVideo(videoId, videoUrl) {
            if (navigator.share) {
                navigator.share({
                    title: 'Check out this video!',
                    url: videoUrl
                }).then(() => {
                    incrementShareCount(videoId);
                }).catch((error) => {
                    console.error('Error sharing video:', error);
                });
            } else {
                alert('Sharing is not supported in your browser.');
            }
        }

        // React to video (like/dislike)
        function reactToVideo(videoId, type) {
            fetch('react.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ video_id: videoId, type: type })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to react to video.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Show comments overlay
        function showComments(videoId) {
            fetch(`fetch_comments.php?video_id=${videoId}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('commentsContainer').innerHTML = html;
                    document.getElementById('commentsOverlay').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error fetching comments:', error);
                });
        }

        // Close comments overlay
        function closeComments() {
            document.getElementById('commentsOverlay').style.display = 'none';
        }

        // Close overlay when clicking outside
        document.getElementById('commentsOverlay').addEventListener('click', (e) => {
            if (e.target === document.getElementById('commentsOverlay')) {
                closeComments();
            }
        });

        // Submit a new comment for a video
        function submitComment(videoId) {
            const commentText = document.getElementById('newCommentText').value;
            if (!commentText.trim()) {
                alert('Comment cannot be empty.');
                return;
            }

            fetch('submit_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ post_id: videoId, content: commentText, type: 'video' })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showComments(videoId); // Refresh comments
                        document.getElementById('newCommentText').value = ''; // Clear input
                    } else {
                        alert('Failed to submit comment.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
</body>

</html>
<?php require 'includes/bottom_nav.php'; ?>