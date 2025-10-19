<?php
// forgot_password.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
</head>

<body>
    <h2>Forgot Password</h2>
    <form action="forgot_password_process.php" method="POST">
        <input type="text" name="identifier" placeholder="Email or Phone" required>
        <button type="submit">Send Reset</button>
    </form>
</body>

</html>