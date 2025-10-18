<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/stories/';
        $file_name = uniqid() . '_' . basename($_FILES['media']['name']);
        $file_path = $upload_dir . $file_name;

        // Move the uploaded file
        if (move_uploaded_file($_FILES['media']['tmp_name'], $file_path)) {
            // Insert the story into the database
            $stmt = $pdo->prepare("INSERT INTO stories (user_id, media_url) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $file_path]);

            header("Location: index.php");
            exit();
        } else {
            echo "Failed to upload file.";
        }
    } else {
        echo "No file uploaded or an error occurred.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Story</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Create a Story</h1>
        <form action="create_story.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="media" class="form-label">Upload Image or Video</label>
                <input type="file" class="form-control" id="media" name="media" accept="image/*, video/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload Story</button>
        </form>
    </div>
</body>

</html>