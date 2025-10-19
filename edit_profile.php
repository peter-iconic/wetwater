<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $full_name = $_POST['full_name'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $location = $_POST['location'] ?? '';
    $interests = $_POST['interests'] ?? '';
    $education = $_POST['education'] ?? '';
    $work = $_POST['work'] ?? '';
    $website = $_POST['website'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/';
        $file_name = basename($_FILES['profile_picture']['name']);
        $file_path = $upload_dir . $file_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
            // Update the profile picture in the database
            $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->execute([$file_name, $user_id]);
        } else {
            echo "<script>alert('Failed to upload profile picture.');</script>";
        }
    }

    // Update other profile information
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, bio = ?, location = ?, interests = ?, education = ?, work = ?, website = ?, phone = ? WHERE id = ?");
    $stmt->execute([$full_name, $bio, $location, $interests, $education, $work, $website, $phone, $user_id]);

    echo "<script>alert('Profile updated successfully!');</script>";
    header("Location: profile.php?id=" . $user_id); // Redirect to profile page
    exit();
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2>Edit Profile</h2>
            <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
                <!-- Profile Picture -->
                <div class="form-group">
                    <label for="profile_picture">Profile Picture</label>
                    <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
                    <?php if (!empty($user['profile_picture'])): ?>
                        <small class="form-text text-muted">Current:
                            <?php echo htmlspecialchars($user['profile_picture']); ?></small>
                    <?php endif; ?>
                </div>

                <!-- Full Name -->
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name"
                        value="<?php echo htmlspecialchars($user['full_name']); ?>">
                </div>

                <!-- Bio -->
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea class="form-control" id="bio" name="bio"
                        rows="3"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                </div>

                <!-- Location -->
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location"
                        value="<?php echo htmlspecialchars($user['location']); ?>">
                </div>

                <!-- Interests -->
                <div class="form-group">
                    <label for="interests">Interests</label>
                    <input type="text" class="form-control" id="interests" name="interests"
                        value="<?php echo htmlspecialchars($user['interests']); ?>">
                </div>

                <!-- Education -->
                <div class="form-group">
                    <label for="education">Education</label>
                    <input type="text" class="form-control" id="education" name="education"
                        value="<?php echo htmlspecialchars($user['education']); ?>">
                </div>

                <!-- Work -->
                <div class="form-group">
                    <label for="work">Work</label>
                    <input type="text" class="form-control" id="work" name="work"
                        value="<?php echo htmlspecialchars($user['work']); ?>">
                </div>

                <!-- Website -->
                <div class="form-group">
                    <label for="website">Website</label>
                    <input type="url" class="form-control" id="website" name="website"
                        value="<?php echo htmlspecialchars($user['website']); ?>">
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>