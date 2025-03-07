<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to send a message.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);

    if (empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']);
        exit();
    }

    // Insert the message into the database
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $receiver_id, $message]);

    echo json_encode(['status' => 'success', 'message' => 'Message sent.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>