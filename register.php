<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Your Website</title>
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

        .register-container {
            background-color: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .register-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .register-header {
            background: linear-gradient(to right, var(--primary-purple), var(--light-purple));
            color: var(--white);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .register-header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            transform: rotate(30deg);
        }

        .register-header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 2.2rem;
            position: relative;
            z-index: 1;
        }

        .register-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .register-body {
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

        .btn-register {
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

        .btn-register:hover {
            background: linear-gradient(to right, var(--light-purple), var(--primary-purple));
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(106, 13, 173, 0.4);
        }

        .btn-register:active {
            transform: translateY(-1px);
        }

        .register-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
        }

        .register-footer a {
            color: var(--primary-purple);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-block;
        }

        .register-footer a:hover {
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

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .terms-container {
            margin: 20px 0;
            padding: 15px;
            background-color: var(--light-gray);
            border-radius: 10px;
            max-height: 150px;
            overflow-y: auto;
            border: 1px solid #eaeaea;
        }

        .terms-container h5 {
            color: var(--primary-purple);
            margin-bottom: 10px;
        }

        .terms-container p {
            font-size: 0.9rem;
            line-height: 1.5;
            color: var(--text-dark);
        }

        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .terms-checkbox input {
            margin-right: 10px;
            margin-top: 3px;
            accent-color: var(--primary-purple);
        }

        .terms-checkbox label {
            color: var(--text-dark);
            font-weight: 500;
            line-height: 1.4;
        }

        .terms-checkbox a {
            color: var(--primary-purple);
            text-decoration: none;
        }

        .terms-checkbox a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .register-container {
                margin: 10px;
            }

            .register-body {
                padding: 30px 25px;
            }

            .register-header {
                padding: 30px 20px;
            }

            .register-header h1 {
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

        .password-strength {
            margin-top: 5px;
            height: 5px;
            border-radius: 5px;
            background-color: #e0e0e0;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background-color 0.3s;
            border-radius: 5px;
        }

        .password-strength-text {
            font-size: 0.8rem;
            margin-top: 5px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-header">
            <i class="fas fa-user-plus floating-icon icon-1"></i>
            <i class="fas fa-file-signature floating-icon icon-2"></i>
            <h1><i class="fas fa-user-plus me-2"></i>Create Account</h1>
            <p>Join our community today</p>
        </div>

        <div class="register-body">
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

            <form action="register_process.php" method="POST" id="registerForm">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Choose a username" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
                            required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Create a password" required>
                        <span class="input-group-text password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                    <div class="password-strength-text" id="passwordStrengthText"></div>
                </div>

                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                            placeholder="Confirm your password" required>
                    </div>
                    <div class="text-danger small mt-1" id="passwordMatchError"></div>
                </div>

                <div class="terms-container">
                    <h5>Terms and Conditions</h5>
                    <p>By creating an account, you agree to our Terms of Service and Privacy Policy. You acknowledge
                        that:</p>
                    <ul>
                        <li>You are responsible for maintaining the confidentiality of your account</li>
                        <li>You will provide accurate and complete information</li>
                        <li>You will not use the service for any illegal activities</li>
                        <li>We may send you service-related communications</li>
                        <li>We reserve the right to modify these terms at any time</li>
                    </ul>
                    <p>Please read our full <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a> for more
                        information.</p>
                </div>

                <div class="terms-checkbox">
                    <input type="checkbox" id="agreeTerms" name="agreeTerms" required>
                    <label for="agreeTerms">I have read and agree to the Terms and Conditions</label>
                </div>

                <button type="submit" class="btn btn-register">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
            </form>

            <div class="register-footer">
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
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

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function () {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');

            let strength = 0;
            let text = '';
            let color = '';

            // Check password length
            if (password.length >= 8) strength += 25;

            // Check for lowercase letters
            if (/[a-z]/.test(password)) strength += 25;

            // Check for uppercase letters
            if (/[A-Z]/.test(password)) strength += 25;

            // Check for numbers and special characters
            if (/[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password)) strength += 25;

            // Set strength text and color
            if (password.length === 0) {
                text = '';
                color = 'transparent';
            } else if (strength <= 25) {
                text = 'Weak';
                color = '#ff4757';
            } else if (strength <= 50) {
                text = 'Fair';
                color = '#ffa502';
            } else if (strength <= 75) {
                text = 'Good';
                color = '#2ed573';
            } else {
                text = 'Strong';
                color = '#2ed573';
            }

            strengthBar.style.width = strength + '%';
            strengthBar.style.backgroundColor = color;
            strengthText.textContent = text;
            strengthText.style.color = color;
        });

        // Password confirmation check
        document.getElementById('confirmPassword').addEventListener('input', function () {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const errorElement = document.getElementById('passwordMatchError');

            if (confirmPassword && password !== confirmPassword) {
                errorElement.textContent = 'Passwords do not match';
            } else {
                errorElement.textContent = '';
            }
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const agreeTerms = document.getElementById('agreeTerms').checked;

            if (!username || !email || !password || !confirmPassword) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
                return;
            }

            if (!agreeTerms) {
                e.preventDefault();
                alert('You must agree to the Terms and Conditions to register.');
                return;
            }

            // Simple email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }

            // Password strength validation
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return;
            }
        });

        // Add loading state to submit button
        document.getElementById('registerForm').addEventListener('submit', function () {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';
            submitBtn.disabled = true;
        });
    </script>
</body>

</html>