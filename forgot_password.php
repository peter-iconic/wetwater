<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Your Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-purple: #6a0dad;
            --light-purple: #8a2be2;
            --dark-purple: #4b0082;
            --accent-purple: #9370db;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --text-dark: #333333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--dark-purple) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .forgot-password-container {
            background-color: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .forgot-password-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .forgot-password-header {
            background: linear-gradient(to right, var(--primary-purple), var(--light-purple));
            color: var(--white);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .forgot-password-header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            transform: rotate(30deg);
        }

        .forgot-password-header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 2.2rem;
            position: relative;
            z-index: 1;
        }

        .forgot-password-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .forgot-password-body {
            padding: 40px 35px;
        }

        .instruction-text {
            color: var(--text-dark);
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .form-control {
            border-radius: 12px;
            padding: 14px 18px;
            border: 2px solid #e1e5ee;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 0.25rem rgba(106, 13, 173, 0.15);
        }

        .input-group-text {
            background-color: var(--white);
            border: 2px solid #e1e5ee;
            border-right: none;
            color: var(--primary-purple);
        }

        .input-group .form-control {
            border-left: none;
        }

        .btn-reset {
            background: linear-gradient(to right, var(--primary-purple), var(--light-purple));
            border: none;
            color: var(--white);
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            width: 100%;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .btn-reset:hover {
            background: linear-gradient(to right, var(--light-purple), var(--primary-purple));
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(106, 13, 173, 0.4);
        }

        .btn-reset:active {
            transform: translateY(-1px);
        }

        .forgot-password-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
        }

        .forgot-password-footer a {
            color: var(--primary-purple);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-block;
        }

        .forgot-password-footer a:hover {
            color: var(--dark-purple);
            text-decoration: underline;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 14px 18px;
            font-weight: 500;
        }

        .alert-success {
            background-color: rgba(76, 175, 80, 0.1);
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }

        .alert-danger {
            background-color: rgba(244, 67, 54, 0.1);
            color: #c62828;
            border-left: 4px solid #f44336;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        @media (max-width: 576px) {
            .forgot-password-container {
                margin: 10px;
            }

            .forgot-password-body {
                padding: 30px 25px;
            }

            .forgot-password-header {
                padding: 30px 20px;
            }

            .forgot-password-header h1 {
                font-size: 1.8rem;
            }
        }

        .floating-icon {
            position: absolute;
            opacity: 0.1;
            font-size: 5rem;
            z-index: 0;
        }

        .icon-1 {
            top: 10%;
            right: 10%;
        }

        .icon-2 {
            bottom: 10%;
            left: 10%;
        }

        .info-box {
            background-color: rgba(106, 13, 173, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            border-left: 4px solid var(--primary-purple);
        }

        .info-box p {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .info-box i {
            color: var(--primary-purple);
            margin-right: 8px;
        }
    </style>
</head>

<body>
    <div class="forgot-password-container">
        <div class="forgot-password-header">
            <i class="fas fa-key floating-icon icon-1"></i>
            <i class="fas fa-unlock-alt floating-icon icon-2"></i>
            <h1><i class="fas fa-key me-2"></i>Reset Password</h1>
            <p>We'll help you get back into your account</p>
        </div>

        <div class="forgot-password-body">
            <?php
            // Display success or error messages
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success mb-4"><i class="fas fa-check-circle me-2"></i>' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger mb-4"><i class="fas fa-exclamation-circle me-2"></i>' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <p class="instruction-text">
                Enter your email address or phone number associated with your account, and we'll send you instructions
                to reset your password.
            </p>

            <form action="forgot_password_process.php" method="POST" id="forgotPasswordForm">
                <div class="mb-4">
                    <label for="identifier" class="form-label">Email or Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="text" class="form-control" id="identifier" name="identifier"
                            placeholder="Enter your email or phone number" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-reset">
                    <i class="fas fa-paper-plane me-2"></i>Send Reset Instructions
                </button>
            </form>

            <div class="info-box">
                <p><i class="fas fa-info-circle"></i> If you don't receive an email within a few minutes, please check
                    your spam folder.</p>
            </div>

            <div class="forgot-password-footer">
                <p>Remember your password? <a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('forgotPasswordForm').addEventListener('submit', function (e) {
            const identifier = document.getElementById('identifier').value;

            if (!identifier) {
                e.preventDefault();
                alert('Please enter your email or phone number.');
                return;
            }

            // Simple validation for email or phone
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/; // Basic international phone format

            if (!emailRegex.test(identifier) && !phoneRegex.test(identifier.replace(/\s/g, ''))) {
                e.preventDefault();
                alert('Please enter a valid email address or phone number.');
                return;
            }
        });

        // Add loading state to submit button
        document.getElementById('forgotPasswordForm').addEventListener('submit', function () {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            submitBtn.disabled = true;
        });

        // Auto-detect if input is email or phone and update icon accordingly
        document.getElementById('identifier').addEventListener('input', function () {
            const value = this.value;
            const icon = document.querySelector('.input-group-text i');

            // Simple check for phone number (contains only numbers, +, spaces, and hyphens)
            const phoneRegex = /^[\+\s\-0-9]+$/;

            if (phoneRegex.test(value) && value.length >= 5) {
                icon.className = 'fas fa-phone';
            } else {
                icon.className = 'fas fa-envelope';
            }
        });
    </script>
</body>

</html>