<?php

session_start();
include 'config/db.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Social App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">TypedPaper</a>
            <div class="navbar-nav me-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    // Fetch the number of unread notifications
                    $stmt = $pdo->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
                    $stmt->execute([$_SESSION['user_id']]);
                    $unread_count = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
                    ?>
                    <a class="nav-link" href="profile.php?id=<?php echo $_SESSION['user_id']; ?>">Profile</a>
                    <a class="nav-link" href="messages.php">Messages</a>
                    <a class="nav-link" href="friends.php">Friends</a>
                    <a class="nav-link" href="notifications.php">
                        Notifications <?php if ($unread_count > 0): ?><span
                                class="badge bg-danger"><?php echo $unread_count; ?></span><?php endif; ?>
                    </a>
                    <a class="nav-link" href="ad_analytics.php">Ad Analytics</a>
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link" href="register.php">Register</a>
                <?php endif; ?>
            </div>
            <!-- Search Bar -->
            <form action="search.php" method="GET" class="d-flex">
                <input class="form-control me-2" type="search" name="query" placeholder="Search posts or users..."
                    aria-label="Search">
                <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
        </div>
    </nav>
    <div class="container mt-4">