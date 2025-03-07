<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="text-center mb-4">User Management</h1>

<!-- Display Users -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Joined</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo $user['created_at']; ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>