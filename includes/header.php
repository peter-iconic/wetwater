<?php
session_start();
include 'config/db.php';

// Update last_activity timestamp for the logged-in user
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Social App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
    <style>
        /* Custom CSS to prevent navbar from collapsing into a dropdown */
        .navbar-nav {
            flex-direction: row;
            gap: 10px;
            /* Adjust spacing between icons */
        }

        .navbar-toggler {
            display: none;
            /* Hide the toggler button */
        }

        .navbar-collapse {
            display: flex !important;
            /* Force the navbar to always show */
        }

        /* Custom CSS for the search bar */
        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input {
            width: 0;
            transition: width 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }

        .search-input.expanded {
            width: 200px;
            /* Adjust width as needed */
            opacity: 1;
            visibility: visible;
        }

        .search-icon {
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">WetWater</a>
            <!-- Navbar Links -->
            <div class="navbar-nav me-auto d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    // Fetch the number of unread notifications
                    $stmt = $pdo->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
                    $stmt->execute([$_SESSION['user_id']]);
                    $unread_count = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
                    ?>
                    <!-- Profile -->
                    <a class="nav-link" href="profile.php?id=<?php echo $_SESSION['user_id']; ?>" title="Profile">
                        <i class="bi bi-person"></i>
                        <span class="d-none d-lg-inline">Profile</span>
                    </a>
                    <!-- Messages -->
                    <a class="nav-link" href="messages.php" title="Messages">
                        <i class="bi bi-chat"></i>
                        <span class="d-none d-lg-inline">Messages</span>
                    </a>
                    <!-- Friends -->
                    <a class="nav-link" href="friends.php" title="Friends">
                        <i class="bi bi-people"></i>
                        <span class="d-none d-lg-inline">people</span>
                    </a>
                    <!-- Notifications -->
                    <a class="nav-link position-relative" href="notifications.php" title="Notifications">
                        <i class="bi bi-bell"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $unread_count; ?>
                            </span>
                        <?php endif; ?>
                        <span class="d-none d-lg-inline">Notifications</span>
                    </a>
                    <!-- Ad Analytics -->
                    <a class="nav-link" href="ad_analytics.php" title="Ad Analytics">
                        <i class="bi bi-bar-chart"></i>
                        <span class="d-none d-lg-inline">Ad Analytics</span>
                    </a> <a class="nav-link" href="create_ad.php" title="Ad Analytics">
                        <i class="bi bi-bar-chart"></i>
                        <span class="d-none d-lg-inline">create ad</span>
                    </a>
                    <!-- Logout -->
                    <a class="nav-link" href="logout.php" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="d-none d-lg-inline">Logout</span>
                    </a>
                <?php else: ?>
                    <!-- Login -->
                    <a class="nav-link" href="login.php" title="Login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span class="d-none d-lg-inline">Login</span>
                    </a>
                    <!-- Register -->
                    <a class="nav-link" href="register.php" title="Register">
                        <i class="bi bi-person-plus"></i>
                        <span class="d-none d-lg-inline">Register</span>
                    </a>
                <?php endif; ?>
            </div>
            <!-- Search Bar -->
            <div class="search-container">
                <form action="search.php" method="GET" class="d-flex">
                    <input class="form-control me-2 search-input" type="search" name="query"
                        placeholder="Search posts or users..." aria-label="Search">
                    <button class="btn btn-outline-light search-icon" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <div class="container mt-4">

        <script>
            // JavaScript to handle search bar expansion and collapse
            document.addEventListener('DOMContentLoaded', function () {
                const searchIcon = document.querySelector('.search-icon');
                const searchInput = document.querySelector('.search-input');
                const searchContainer = document.querySelector('.search-container');

                // Expand search bar when icon is clicked
                searchIcon.addEventListener('click', function (e) {
                    e.stopPropagation(); // Prevent the click from propagating to the document
                    searchInput.classList.toggle('expanded');
                    if (searchInput.classList.contains('expanded')) {
                        searchInput.focus(); // Focus on the input when expanded
                    }
                });

                // Collapse search bar when clicking outside
                document.addEventListener('click', function (e) {
                    if (!searchContainer.contains(e.target)) {
                        searchInput.classList.remove('expanded');
                    }
                });
            });
        </script>