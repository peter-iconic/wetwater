<?php
// Include necessary configurations or session checks if needed
?>

<!-- Bottom Navigation Bar -->
<nav class="navbar fixed-bottom navbar-expand navbar-dark bg-purple d-block d-md-none" style="bottom: -20px;">
    <div class="container-fluid">
        <div class="row w-100 g-0"> <!-- Added g-0 to remove gutters -->
            <!-- Home Button -->
            <div class="col text-center">
                <a href="index.php" class="btn btn-purple w-100 text-white">
                    <i class="bi bi-house-door"></i><br>
                    <small>Home</small>
                </a>
            </div>

            <!-- Videos Button -->
            <div class="col text-center">
                <a href="videos.php" class="btn btn-purple w-100 text-white">
                    <i class="bi bi-play-circle"></i><br>
                    <small>Videos</small>
                </a>
            </div>

            <!-- Plus Button (Centered) -->
            <div class="col text-center">
                <button type="button" class="btn btn-primary rounded-circle text-white"
                    style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; margin: -10px auto; background-color: #6f42c1; border: none;"
                    data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-lg" style="font-size: 1.5rem;"></i>
                </button>
            </div>

            <!-- Messages Button -->
            <div class="col text-center">
                <a href="messages.php" class="btn btn-purple w-100 text-white">
                    <i class="bi bi-chat"></i><br>
                    <small>Messages</small>
                </a>
            </div>

            <!-- Profile Button -->
            <div class="col text-center">
                <a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-purple w-100 text-white">
                    <i class="bi bi-person"></i><br>
                    <small>Profile</small>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Modal for Create Options -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Create New</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Create Post Option -->
                <a href="create_post.php" class="btn btn-light w-100 text-start mb-2">
                    <i class="bi bi-pencil-square me-2 text-purple"></i> Upload photo
                </a>
                <!-- Upload Video Option -->
                <a href="upload_video.php" class="btn btn-light w-100 text-start mb-2">
                    <i class="bi bi-camera-video me-2 text-purple"></i> Upload Video
                </a>
                <!-- Create Story Option -->
                <a href="create_story.php" class="btn btn-light w-100 text-start">
                    <i class="bi bi-plus-circle me-2 text-purple"></i> Create Story
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Optional: Add custom CSS for better styling -->
<style>
    .navbar.fixed-bottom {
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        background-color: #6f42c1;
        /* Purple background */
    }

    .navbar .btn {
        padding: 0.25rem;
        font-size: 0.8rem;
        color: white;
    }

    .navbar .btn i {
        font-size: 1rem;
        color: white;
    }

    .navbar .btn small {
        display: block;
        margin-top: 0.1rem;
        color: white;
    }

    /* Style for the plus button */
    .btn-primary.rounded-circle {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        background-color: #6f42c1;
        border: none;
    }

    /* Remove extra spacing between columns */
    .row.g-0>.col {
        padding-left: 0;
        padding-right: 0;
    }

    .text-purple {
        color: #6f42c1 !important;
    }

    .btn-purple {
        background-color: #6f42c1 !important;
        border-color: #6f42c1 !important;
    }
</style>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>