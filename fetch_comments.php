<?php
session_start();
include 'config/db.php';

// Check if post_id and type are provided
if (!isset($_GET['post_id']) || !isset($_GET['type'])) {
    die("Post ID and type are required.");
}

$postId = (int) $_GET['post_id'];
$type = $_GET['type']; // 'post' or 'video'

// Add comment input form
echo "
    <div class='add-comment-form'>
        <textarea id='newCommentText' placeholder='Add a comment...'></textarea>
        <button onclick='submitComment($postId, \"$type\")'>Submit</button>
    </div>
";

try {
    // Fetch top-level comments (comments without a parent)
    $stmt = $pdo->prepare("
        SELECT comments.*, users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE post_id = ? AND type = ? AND parent_comment_id IS NULL
        ORDER BY created_at DESC
    ");
    $stmt->execute([$postId, $type]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($comments)) {
        echo "<p>No comments yet. Be the first to comment!</p>";
    } else {
        foreach ($comments as $comment) {
            echo renderComment($comment);

            // Fetch replies to this comment
            $stmt = $pdo->prepare("
                SELECT comments.*, users.username 
                FROM comments 
                JOIN users ON comments.user_id = users.id 
                WHERE parent_comment_id = ?
                ORDER BY created_at ASC
            ");
            $stmt->execute([$comment['id']]);
            $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($replies)) {
                echo '<div class="replies" style="margin-left: 20px;">';
                foreach ($replies as $reply) {
                    echo renderComment($reply);
                }
                echo '</div>';
            }
        }
    }
} catch (PDOException $e) {
    echo "Error fetching comments: " . $e->getMessage();
}

// Function to render a comment
function renderComment($comment)
{
    $reactions = json_decode($comment['reactions'], true);
    $likes = $reactions['likes'] ?? 0;
    $dislikes = $reactions['dislikes'] ?? 0;

    return "
        <div class='comment' data-comment-id='{$comment['id']}'>
            <strong>{$comment['username']}</strong>: {$comment['content']}
            <div class='comment-actions'>
                <button onclick='reactToComment({$comment['id']}, \"like\")'>👍 $likes</button>
                <button onclick='reactToComment({$comment['id']}, \"dislike\")'>👎 $dislikes</button>
                <button onclick='showReplyForm({$comment['id']})'>Reply</button>
                " . ($comment['user_id'] == $_SESSION['user_id'] ? "
                    <button onclick='editComment({$comment['id']})'>Edit</button>
                    <button onclick='deleteComment({$comment['id']})'>Delete</button>
                " : "") . "
            </div>
            <div id='replyForm{$comment['id']}' class='reply-form' style='display: none;'>
                <textarea id='replyText{$comment['id']}' placeholder='Write a reply...'></textarea>
                <button onclick='submitReply({$comment['id']})'>Submit</button>
            </div>
        </div>
    ";
}
?>