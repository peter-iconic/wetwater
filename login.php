<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Your Website</title>
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

        .login-container {
            background-color: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .login-header {
            background: linear-gradient(to right, var(--primary-purple), var(--light-purple));
            color: var(--white);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            transform: rotate(30deg);
        }

        .login-header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 2.2rem;
            position: relative;
            z-index: 1;
        }

        .login-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .login-body {
            padding: 40px 35px;
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

        .btn-login {
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

        .btn-login:hover {
            background: linear-gradient(to right, var(--light-purple), var(--primary-purple));
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(106, 13, 173, 0.4);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
        }

        .login-footer a {
            color: var(--primary-purple);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-block;
        }

        .login-footer a:hover {
            color: var(--dark-purple);
            text-decoration: underline;
            transform: translateY(-2px);
        }

        .password-toggle {
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s;
            background-color: var(--white);
            border: 2px solid #e1e5ee;
            border-left: none;
        }

        .password-toggle:hover {
            color: var(--primary-purple);
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

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me input {
            margin-right: 10px;
            accent-color: var(--primary-purple);
        }

        .remember-me label {
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .forgot-password {
            color: var(--primary-purple);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .forgot-password:hover {
            color: var(--dark-purple);
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .login-container {
                margin: 10px;
            }

            .login-body {
                padding: 30px 25px;
            }

            .login-header {
                padding: 30px 20px;
            }

            .login-header h1 {
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
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-user-circle floating-icon icon-1"></i>
            <i class="fas fa-lock floating-icon icon-2"></i>
            <h1><i class="fas fa-user-circle me-2"></i>Welcome Back</h1>
            <p>Sign in to access your account</p>
        </div>

        <div class="login-body">
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

            <form action="login_process.php" method="POST" id="loginForm">
                <div class="mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
                            required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Enter your password" required>
                        <span class="input-group-text password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>

            <div class="login-footer">
                <p>Don't have an account? <a href="register.php">Create one here</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }

            // Simple email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }
        });

        // Add loading state to submit button
        document.getElementById('loginForm').addEventListener('submit', function () {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
            submitBtn.disabled = true;
        });
    </script>
</body>

</html>