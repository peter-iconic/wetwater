<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}
?>

<h1 class="text-center mb-4">Admin Dashboard</h1>

<div class="row">
    <!-- User Management -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">User Management</h5>
                <p class="card-text">Manage users, view profiles, and edit or delete accounts.</p>
                <a href="admin_users.php" class="btn btn-primary">Go to Users</a>
            </div>
        </div>
    </div>

    <!-- Post Management -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Post Management</h5>
                <p class="card-text">Manage posts, view content, and edit or delete posts.</p>
                <a href="admin_posts.php" class="btn btn-primary">Go to Posts</a>
            </div>
        </div>
    </div>

    <!-- Comment Management -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Comment Management</h5>
                <p class="card-text">Manage comments, view content, and edit or delete comments.</p>
                <a href="admin_comments.php" class="btn btn-primary">Go to Comments</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>