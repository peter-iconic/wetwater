<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if an ad ID is provided
if (!isset($_GET['id'])) {
    die("Ad ID not specified.");
}

$ad_id = $_GET['id'];

// Fetch the ad's details
$stmt = $pdo->prepare("SELECT * FROM ads WHERE id = ? AND user_id = ?");
$stmt->execute([$ad_id, $_SESSION['user_id']]);
$ad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ad) {
    die("Ad not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $link = trim($_POST['link']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Validate inputs
    if (empty($title) || empty($link) || empty($start_time) || empty($end_time)) {
        $_SESSION['error'] = "All fields are required.";
    } else {
        // Update the ad
        try {
            $stmt = $pdo->prepare("UPDATE ads SET title = ?, description = ?, link = ?, start_time = ?, end_time = ? WHERE id = ?");
            $stmt->execute([$title, $description, $link, $start_time, $end_time, $ad_id]);

            $_SESSION['success'] = "Ad updated successfully!";
            header("Location: manage_ads.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Failed to update ad. Please try again.";
        }
    }
}
?>

<h1 class="text-center mb-4">Edit Ad</h1>

<!-- Display success or error messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Edit Ad Form -->
<form action="edit_ad.php?id=<?php echo $ad_id; ?>" method="POST">
    <div class="mb-3">
        <label for="title" class="form-label">Ad Title</label>
        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($ad['title']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Ad Description</label>
        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($ad['description']); ?></textarea>
    </div>
    <div class="mb-3">
        <label for="link" class="form-label">Ad Link</label>
        <input type="url" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($ad['link']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="start_time" class="form-label">Start Time</label>
        <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($ad['start_time'])); ?>" required>
    </div>
    <div class="mb-3">
        <label for="end_time" class="form-label">End Time</label>
        <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="<?php echo date('Y-m-d\TH:i', strtotime($ad['end_time'])); ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>

<?php include 'includes/footer.php'; ?>