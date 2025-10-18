<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Fetch all comments
$stmt = $pdo->query("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id ORDER BY comments.created_at DESC");
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="text-center mb-4">Comment Management</h1>

<!-- Display Comments -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Content</th>
            <th>Author</th>
            <th>Posted</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($comments as $comment): ?>
            <tr>
                <td><?php echo $comment['id']; ?></td>
                <td><?php echo htmlspecialchars($comment['content']); ?></td>
                <td><?php echo htmlspecialchars($comment['username']); ?></td>
                <td><?php echo $comment['created_at']; ?></td>
                <td>
                    <a href="edit_comment.php?id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_comment.php?id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this comment?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>