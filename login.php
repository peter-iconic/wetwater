<?php
include 'includes/header.php';

// Display success or error messages
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<h1 class="text-center mb-4">Login</h1>
<form action="login_process.php" method="POST">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>

<?php include 'includes/footer.php'; ?>