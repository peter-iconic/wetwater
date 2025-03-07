<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the search query and filters
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';
$filter_popularity = isset($_GET['filter_popularity']) ? $_GET['filter_popularity'] : '';
$filter_user = isset($_GET['filter_user']) ? trim($_GET['filter_user']) : '';

// Fetch search results
if (!empty($query)) {
    // Build the base query for posts
    $post_query = "
        SELECT posts.*, users.username
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE (posts.content LIKE ? OR users.username LIKE ?)
    ";

    // Apply date filter
    if (!empty($filter_date)) {
        $post_query .= " AND posts.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
    }

    // Apply user filter
    if (!empty($filter_user)) {
        $post_query .= " AND users.username LIKE ?";
    }

    // Apply popularity filter
    if (!empty($filter_popularity)) {
        if ($filter_popularity === 'likes') {
            $post_query .= " ORDER BY (SELECT COUNT(*) FROM reactions WHERE reactions.post_id = posts.id AND reactions.type = 'like') DESC";
        } elseif ($filter_popularity === 'comments') {
            $post_query .= " ORDER BY (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) DESC";
        }
    } else {
        $post_query .= " ORDER BY posts.created_at DESC";
    }

    // Execute the post query
    $stmt = $pdo->prepare($post_query);
    $params = ["%$query%", "%$query%"];
    if (!empty($filter_date)) {
        $params[] = $filter_date;
    }
    if (!empty($filter_user)) {
        $params[] = "%$filter_user%";
    }
    $stmt->execute($params);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Search for users
    $user_query = "
        SELECT * FROM users
        WHERE username LIKE ?
    ";
    if (!empty($filter_user)) {
        $user_query .= " AND username LIKE ?";
    }
    $user_query .= " ORDER BY username ASC";

    $stmt = $pdo->prepare($user_query);
    $user_params = ["%$query%"];
    if (!empty($filter_user)) {
        $user_params[] = "%$filter_user%";
    }
    $stmt->execute($user_params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $posts = [];
    $users = [];
}
?>

<h1 class="text-center mb-4">Search Results</h1>

<!-- Search and Filters Form -->
<form action="search.php" method="GET" class="mb-4">
    <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
    <div class="row">
        <div class="col-md-3">
            <label for="filter_date" class="form-label">Filter by Date</label>
            <select class="form-select" id="filter_date" name="filter_date">
                <option value="">All Time</option>
                <option value="1" <?php echo ($filter_date === '1') ? 'selected' : ''; ?>>Last 24 Hours</option>
                <option value="7" <?php echo ($filter_date === '7') ? 'selected' : ''; ?>>Last 7 Days</option>
                <option value="30" <?php echo ($filter_date === '30') ? 'selected' : ''; ?>>Last 30 Days</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="filter_popularity" class="form-label">Filter by Popularity</label>
            <select class="form-select" id="filter_popularity" name="filter_popularity">
                <option value="">All Posts</option>
                <option value="likes" <?php echo ($filter_popularity === 'likes') ? 'selected' : ''; ?>>Most Liked
                </option>
                <option value="comments" <?php echo ($filter_popularity === 'comments') ? 'selected' : ''; ?>>Most
                    Commented</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="filter_user" class="form-label">Filter by User</label>
            <input type="text" class="form-control" id="filter_user" name="filter_user" placeholder="Enter username"
                value="<?php echo htmlspecialchars($filter_user); ?>">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary mt-4">Apply Filters</button>
        </div>
    </div>
</form>

<!-- Display Search Results -->
<?php if (empty($query)): ?>
    <p class="text-center">Please enter a search term.</p>
<?php else: ?>
    <!-- Display Posts -->
    <h3>Posts</h3>
    <?php if (empty($posts)): ?>
        <p>No posts found.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="profile.php?id=<?php echo $post['user_id']; ?>">
                            <?php echo htmlspecialchars($post['username']); ?>
                        </a>
                    </h5>
                    <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                    <small class="text-muted">Posted on: <?php echo $post['created_at']; ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Display Users -->
    <h3>Users</h3>
    <?php if (empty($users)): ?>
        <p>No users found.</p>
    <?php else: ?>
        <?php foreach ($users as $user): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="profile.php?id=<?php echo $user['id']; ?>">
                            <?php echo htmlspecialchars($user['username']); ?>
                        </a>
                    </h5>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>