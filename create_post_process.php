<?php
// Start the session
session_start();

// Include the database connection
include 'config/db.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $image = null;

    // Validate content
    if (empty($content)) {
        $_SESSION['error'] = "Post content cannot be empty.";
        header("Location: create_post.php");
        exit();
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Define upload directory and allowed file types
        $upload_dir = 'assets/images/posts/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];

        // Validate file type
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error'] = "Only JPEG, PNG, and GIF images are allowed.";
            header("Location: create_post.php");
            exit();
        }

        // Create the upload directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Generate a unique file name
        $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $file_path = $upload_dir . $file_name;

        // Move the uploaded file to the upload directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
            $image = $file_path;
        } else {
            $_SESSION['error'] = "Failed to upload image.";
            header("Location: create_post.php");
            exit();
        }
    }

    // Insert the post into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $content, $image]);

        // Set success message
        $_SESSION['success'] = "Post created successfully!";
    } catch (PDOException $e) {
        // Handle database errors
        $_SESSION['error'] = "Failed to create post. Please try again.";
    }
} else {
    // Redirect if the form is not submitted
    $_SESSION['error'] = "Invalid request.";
}

// Redirect back to the homepage
header("Location: index.php");
exit();
?>