<?php
// Start the session

// Include the header
include 'includes/header.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<h1 class="text-center mb-4">Create a New Post</h1>

<!-- Display success or error messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success'];
    unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error'];
    unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Post Creation Form -->
<form action="create_post_process.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="content" class="form-label">What's on your mind?</label>
        <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Upload Image</label>
        <input type="file" class="form-control" id="image" name="image">
    </div>
    <button type="submit" class="btn btn-primary">Post</button>
</form>

<?php include 'includes/footer.php'; ?>