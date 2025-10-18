<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to perform this action.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];

    switch ($action) {
        case 'send':
            $receiver_id = $_POST['receiver_id'];
            if ($user_id == $receiver_id) {
                echo json_encode(['status' => 'error', 'message' => 'You cannot send a friend request to yourself.']);
                exit();
            }

            // Check if a request already exists
            $stmt = $pdo->prepare("SELECT * FROM friend_requests WHERE sender_id = ? AND receiver_id = ?");
            $stmt->execute([$user_id, $receiver_id]);
            if ($stmt->fetch()) {
                echo json_encode(['status' => 'error', 'message' => 'Friend request already sent.']);
                exit();
            }

            // Insert the friend request
            $stmt = $pdo->prepare("INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$user_id, $receiver_id]);
            echo json_encode(['status' => 'success', 'message' => 'Friend request sent.']);
            break;

        case 'accept':
            $request_id = $_POST['request_id'];
            // Fetch the request
            $stmt = $pdo->prepare("SELECT * FROM friend_requests WHERE id = ? AND receiver_id = ? AND status = 'pending'");
            $stmt->execute([$request_id, $user_id]);
            $request = $stmt->fetch();

            if (!$request) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
                exit();
            }

            // Update the request status to 'accepted'
            $stmt = $pdo->prepare("UPDATE friend_requests SET status = 'accepted' WHERE id = ?");
            $stmt->execute([$request_id]);

            // Add the friend relationship
            $stmt = $pdo->prepare("INSERT INTO friends (user_id, friend_id) VALUES (?, ?), (?, ?)");
            $stmt->execute([$user_id, $request['sender_id'], $request['sender_id'], $user_id]);
            echo json_encode(['status' => 'success', 'message' => 'Friend request accepted.']);
            break;

        case 'decline':
            $request_id = $_POST['request_id'];
            // Delete the request
            $stmt = $pdo->prepare("DELETE FROM friend_requests WHERE id = ? AND receiver_id = ?");
            $stmt->execute([$request_id, $user_id]);
            echo json_encode(['status' => 'success', 'message' => 'Friend request declined.']);
            break;

        case 'remove':
            $friend_id = $_POST['friend_id'];
            // Remove the friend relationship
            $stmt = $pdo->prepare("DELETE FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
            $stmt->execute([$user_id, $friend_id, $friend_id, $user_id]);
            echo json_encode(['status' => 'success', 'message' => 'Friend removed.']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>