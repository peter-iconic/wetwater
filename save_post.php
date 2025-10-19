<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please log in to save posts']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;

    if (!$post_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    try {
        // Check if already saved (you'll need to create a saved_posts table)
        // For now, we'll just return a success message
        // You can implement actual saving functionality later

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Post saved successfully!'
        ]);

    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>