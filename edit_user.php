<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Check if a user ID is provided
if (!isset($_GET['id'])) {
    die("User ID not specified.");
}

$user_id = $_GET['id'];

// Fetch the user's details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Validate inputs
    if (empty($username) || empty($email)) {
        $_SESSION['error'] = "Username and email are required.";
    } else {
        // Update user details
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, is_admin = ? WHERE id = ?");
            $stmt->execute([$username, $email, $is_admin, $user_id]);

            $_SESSION['success'] = "User updated successfully!";
            header("Location: admin_users.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Failed to update user. Please try again.";
        }
    }
}
?>

<h1 class="text-center mb-4">Edit User</h1>

<!-- Display success or error messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success'];
    unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error'];
    unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Edit User Form -->
<form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username"
            value="<?php echo htmlspecialchars($user['username']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email"
            value="<?php echo htmlspecialchars($user['email']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="is_admin" class="form-label">Admin</label>
        <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" <?php echo ($user['is_admin']) ? 'checked' : ''; ?>>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>

<?php include 'includes/footer.php'; ?>