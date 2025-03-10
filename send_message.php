<?php
session_start();
include 'config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit();
}

// Get the receiver ID and message from the POST data
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'];

// Handle audio file upload
$audio_path = null;
if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/audio/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $audio_name = uniqid() . '_' . basename($_FILES['audio']['name']);
    $audio_path = $uploadDir . $audio_name;

    if (!move_uploaded_file($_FILES['audio']['tmp_name'], $audio_path)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload audio.']);
        exit();
    }
}

// Fetch the receiver's public key
$stmt = $pdo->prepare("SELECT public_key FROM users WHERE id = ?");
$stmt->execute([$receiver_id]);
$receiver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$receiver || empty($receiver['public_key'])) {
    echo json_encode(['status' => 'error', 'message' => 'Receiver not found.']);
    exit();
}

// Encrypt the message using the receiver's public key
require 'vendor/autoload.php';
use phpseclib3\Crypt\PublicKeyLoader;

try {
    $publicKey = PublicKeyLoader::load($receiver['public_key']);
    $encryptedMessage = base64_encode($publicKey->encrypt($message));
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Encryption failed.']);
    exit();
}

// Insert the message into the database
$stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, sender_message, audio_path) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $receiver_id, $encryptedMessage, $message, $audio_path]);

// Return a success response
echo json_encode(['status' => 'success', 'message' => $message, 'audio_path' => $audio_path]);
?>