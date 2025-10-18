<?php
// Start the session

// Include the header
include 'includes/header.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<h1 class="text-center mb-4">Create a New Post</h1>

<!-- Display success or error messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success'];
    unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error'];
    unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Post Creation Form -->
<form action="create_post_process.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3"> <!-- Textarea with emoji and flag picker -->
        <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
        <!-- Emoji Picker Button -->
        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="emojiPickerBtn">
            <i class="bi bi-emoji-smile"></i> Add Emoji
        </button>
        <!-- Flag Picker Button -->
        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="flagPickerBtn">
            <i class="bi bi-flag"></i> Add Flag
        </button>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Upload Image</label>
        <input type="file" class="form-control" id="image" name="image">
    </div>
    <button type="submit" class="btn btn-primary">Post</button>
</form>

<!-- Include jQuery and Emoji Picker Library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.2/dist/index.min.js"></script>
<script>
    // Initialize Emoji Picker
    const picker = new EmojiButton({
        position: 'bottom-start', // Position of the emoji picker
    });

    // Add Emoji to Textarea
    const emojiPickerBtn = document.getElementById('emojiPickerBtn');
    const contentTextarea = document.getElementById('content');

    emojiPickerBtn.addEventListener('click', () => {
        picker.togglePicker(emojiPickerBtn);
    });

    picker.on('emoji', (emoji) => {
        contentTextarea.value += emoji;
    });

    // Initialize Flag Picker (using emoji picker for flags)
    const flagPickerBtn = document.getElementById('flagPickerBtn');

    flagPickerBtn.addEventListener('click', () => {
        picker.togglePicker(flagPickerBtn);
    });

    picker.on('emoji', (emoji) => {
        if (emoji.includes('flag')) { // Filter for flag emojis
            contentTextarea.value += emoji;
        }
    });
</script>