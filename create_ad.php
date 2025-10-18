<?php

// Include the database connection and header
include 'includes/header.php';

include 'config/db.php';

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

    // Validate start and end times
    if (strtotime($start_time) >= strtotime($end_time)) {
        $_SESSION['error'] = "End time must be after start time.";
        header("Location: create_ad.php");
        exit();
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/ads/';
        $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $file_path = $upload_dir . $file_name;

        // Validate file type and size
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $_SESSION['error'] = "Only JPG, PNG, and GIF images are allowed.";
            header("Location: create_ad.php");
            exit();
        }

        if ($_FILES['image']['size'] > $max_size) {
            $_SESSION['error'] = "Image size must be less than 5MB.";
            header("Location: create_ad.php");
            exit();
        }

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
        $stmt = $pdo->prepare("INSERT INTO ads (user_id, title, description, image, link, start_time, end_time, pricing_plan_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $title, $description, $image, $link, $start_time, $end_time, $pricing_plan_id]);

        $_SESSION['success'] = "Ad created successfully! It will be reviewed by an admin.";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to create ad. Please try again.";
        header("Location: create_ad.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Ad</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
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
                <input type="file" class="form-control" id="image" name="image"
                    accept="image/jpeg, image/png, image/gif">
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
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>