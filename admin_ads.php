<?php
include 'config/db.php';
include 'includes/header.php';

// Redirect if the user is not an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Fetch all ads
$stmt = $pdo->query("SELECT ads.*, users.username FROM ads JOIN users ON ads.user_id = users.id ORDER BY ads.created_at DESC");
$ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="text-center mb-4">Ad Management</h1>

<!-- Display Ads -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Image</th>
            <th>Link</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ads as $ad): ?>
            <tr>
                <td><?php echo $ad['id']; ?></td>
                <td><?php echo htmlspecialchars($ad['title']); ?></td>
                <td><?php echo htmlspecialchars($ad['description']); ?></td>
                <td>
                    <?php if ($ad['image']): ?>
                        <img src="<?php echo $ad['image']; ?>" alt="Ad Image" width="100">
                    <?php endif; ?>
                </td>
                <td><a href="<?php echo htmlspecialchars($ad['link']); ?>"
                        target="_blank"><?php echo htmlspecialchars($ad['link']); ?></a></td>
                <td><?php echo ucfirst($ad['status']); ?></td>
                <td>
                    <?php if ($ad['status'] === 'pending'): ?>
                        <a href="approve_ad.php?id=<?php echo $ad['id']; ?>" class="btn btn-sm btn-success">Approve</a>
                        <a href="reject_ad.php?id=<?php echo $ad['id']; ?>" class="btn btn-sm btn-danger">Reject</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>