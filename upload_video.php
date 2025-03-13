<?php
include 'includes/header.php';

include 'config/db.php'; // Include database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to upload a video.");
    }

    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    // Handle file upload
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['video'];

        // Validate file type and size
        $allowed_types = ['video/mp4', 'video/avi', 'video/mov', 'video/quicktime'];
        $max_size = 50 * 1024 * 1024; // 50MB

        if (!in_array($file['type'], $allowed_types)) {
            die("Invalid file type. Only MP4, AVI, and MOV files are allowed.");
        }

        if ($file['size'] > $max_size) {
            die("File size exceeds the maximum limit of 50MB.");
        }

        // Generate a unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $file_extension;

        // Define upload directory
        $upload_dir = 'assets/videos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // Create the directory if it doesn't exist
        }

        // Move the uploaded file to the upload directory
        $destination = $upload_dir . $filename;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Insert video metadata into the database
            $stmt = $pdo->prepare("INSERT INTO videos (user_id, title, description, video_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $title, $description, $destination]);

            echo "<div class='alert alert-success mt-3'>Video uploaded successfully!</div>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Failed to upload video. Please try again.</div>";
        }
    } else {
        echo "<div class='alert alert-danger mt-3'>No video file uploaded or an error occurred.</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <!-- Include Header -->

    <div class="container mt-5 pt-4">
        <h2 class="mb-4">Upload Video</h2>

        <!-- Video Upload Form -->
        <form action="upload_video.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Video Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="video" class="form-label">Choose Video File</label>
                <input type="file" class="form-control" id="video" name="video" accept="video/*" required>
                <small class="form-text text-muted">Supported formats: MP4, AVI, MOV, etc. (Max size: 50MB)</small>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>

    <!-- Include Footer or Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>