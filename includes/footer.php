<?php
// Include necessary configurations or session checks if needed
?>

<!-- Bottom Navigation Bar -->
<nav class="navbar fixed-bottom navbar-expand navbar-dark bg-black d-block d-md-none" style="bottom: -20px;">
    <div class="container-fluid">
        <div class="row w-100 g-0"> <!-- Added g-0 to remove gutters -->
            <!-- Home Button -->
            <div class="col text-center">
                <a href="index.php" class="btn btn-dark w-100 text-white">
                    <i class="bi bi-house-door"></i><br>
                    <small>Home</small>
                </a>
            </div>

            <!-- Videos Button -->
            <div class="col text-center">
                <a href="videos.php" class="btn btn-dark w-100 text-white">
                    <i class="bi bi-play-circle"></i><br>
                    <small>Videos</small>
                </a>
            </div>

            <!-- Plus Button (Centered) -->
            <div class="col text-center">
                <a href="create_post.php" class="btn btn-primary rounded-circle text-white"
                    style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; margin: -10px auto;">
                    <i class="bi bi-plus-lg" style="font-size: 1.5rem;"></i>
                </a>
            </div>

            <!-- Messages Button -->
            <div class="col text-center">
                <a href="messages.php" class="btn btn-dark w-100 text-white">
                    <i class="bi bi-chat"></i><br>
                    <small>Messages</small>
                </a>
            </div>

            <!-- Profile Button -->
            <div class="col text-center">
                <a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-dark w-100 text-white">
                    <i class="bi bi-person"></i><br>
                    <small>Profile</small>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Include Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Optional: Add custom CSS for better styling -->
<style>
    .navbar.fixed-bottom {
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        background-color: black;
        /* Ensure black background */
    }

    .navbar .btn {
        padding: 0.25rem;
        /* Reduced padding */
        font-size: 0.8rem;
        /* Smaller font size */
        color: white;
        /* White text */
    }

    .navbar .btn i {
        font-size: 1rem;
        /* Smaller icon size */
        color: white;
        /* White icons */
    }

    .navbar .btn small {
        display: block;
        margin-top: 0.1rem;
        /* Reduced margin */
        color: white;
        /* White text */
    }

    /* Style for the plus button */
    .btn-primary.rounded-circle {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        /* Add a shadow for emphasis */
        background-color: #007bff;
        /* Primary color for plus button */
        border: none;
        /* Remove border */
    }

    /* Remove extra spacing between columns */
    .row.g-0>.col {
        padding-left: 0;
        padding-right: 0;
    }
</style>