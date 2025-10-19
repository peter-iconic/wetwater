<?php
require 'config/db.php';

$token = $_GET['token'] ?? '';
$phone = $_GET['phone'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body>
    <h2>Reset Password</h2>
    <form action="reset_password_process.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>

</html>