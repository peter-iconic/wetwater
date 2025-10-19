<?php
require 'config/db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);

    // Find user by email or phone
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :identifier OR phone = :identifier LIMIT 1");
    $stmt->execute(['identifier' => $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found with this email or phone.");
    }

    // Email recovery
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $token = bin2hex(random_bytes(50));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Save token
        $update = $pdo->prepare("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE id = :id");
        $update->execute([
            'token' => $token,
            'expiry' => $expiry,
            'id' => $user['id']
        ]);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'YOUR_GMAIL@gmail.com'; // Your Gmail
            $mail->Password = 'YOUR_APP_PASSWORD'; // Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('YOUR_GMAIL@gmail.com', 'Social App');
            $mail->addAddress($user['email'], $user['username']);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Hi {$user['username']},<br><br>
                Click <a href='http://localhost/Social/reset_password.php?token={$token}'>here</a> to reset your password. This link expires in 1 hour.";

            $mail->send();
            echo "Reset link sent to your email.";
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    // Phone recovery (OTP)
    else {
        $otp = rand(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Save OTP
        $update = $pdo->prepare("UPDATE users SET reset_token = :otp, token_expiry = :expiry WHERE id = :id");
        $update->execute([
            'otp' => $otp,
            'expiry' => $expiry,
            'id' => $user['id']
        ]);

        // Twilio setup
        $sid = "YOUR_TWILIO_SID";
        $token = "YOUR_TWILIO_AUTH_TOKEN";
        $from = "+1234567890"; // Twilio number
        $to = $user['phone'];

        $client = new Client($sid, $token);

        try {
            $client->messages->create(
                $to,
                [
                    'from' => $from,
                    'body' => "Your OTP for password reset is: $otp (valid 10 min)"
                ]
            );
            echo "OTP sent to your phone.";
        } catch (Exception $e) {
            echo "Failed to send OTP: " . $e->getMessage();
        }
    }
}
