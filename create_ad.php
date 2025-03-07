]
<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $link = trim($_POST['link']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $pricing_plan_id = $_POST['pricing_plan'];
    $user_id = $_SESSION['user_id'];
    $image = null;

    // Validate inputs
    if (empty($title) || empty($link) || empty($start_time) || empty($end_time) || empty($pricing_plan_id)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: create_ad.php");
        exit();
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/ads/';
        $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
            $image = $file_path;
        } else {
            $_SESSION['error'] = "Failed to upload image.";
            header("Location: create_ad.php");
            exit();
        }
    }

    // Insert the ad into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO ads (user_id, title, description, image, link, start_time, end_time, pricing_plan_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $description, $image, $link, $start_time, $end_time, $pricing_plan_id]);

        $_SESSION['success'] = "Ad created successfully! It will be reviewed by an admin.";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to create ad. Please try again.";
    }
}
?>

<h1 class="text-center mb-4">Create an Ad</h1>

<!-- Display success or error messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success'];
    unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error'];
    unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Ad Creation Form -->
<form action="create_ad.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="title" class="form-label">Ad Title</label>
        <input type="text" class="form-control" id="title" name="title" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Ad Description</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Ad Image</label>
        <input type="file" class="form-control" id="image" name="image">
    </div>
    <div class="mb-3">
        <label for="link" class="form-label">Ad Link</label>
        <input type="url" class="form-control" id="link" name="link" required>
    </div>
    <div class="mb-3">
        <label for="start_time" class="form-label">Start Time</label>
        <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
    </div>
    <div class="mb-3">
        <label for="end_time" class="form-label">End Time</label>
        <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
    </div>
    <div class="mb-3">
        <label for="pricing_plan" class="form-label">Pricing Plan</label>
        <select class="form-select" id="pricing_plan" name="pricing_plan" required>
            <option value="">Select a pricing plan</option>
            <?php
            $stmt = $pdo->query("SELECT * FROM pricing_plans");
            $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($plans as $plan): ?>
                <option value="<?php echo $plan['id']; ?>">
                    <?php echo htmlspecialchars($plan['name']); ?> - $<?php echo $plan['rate']; ?> per
                    <?php echo $plan['type']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Create Ad</button>
</form>

<?php include 'includes/footer.php'; ?>