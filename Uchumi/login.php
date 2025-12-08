<?php 
include 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $redirect = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'index.php';
    unset($_SESSION['redirect_url']);
    header("Location: $redirect");
    exit();
}

// Process login form
$error = '';
$success = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = "Security token invalid. Please try again.";
    } else {
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) ? true : false;
        
        if (empty($username) || empty($password)) {
            $error = "Please fill in all fields";
        } else {
            // Check user credentials with prepared statement
            $stmt = $conn->prepare("SELECT id, username, password, full_name, email, phone FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Verify password (using password_verify for hashed passwords)
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['login_time'] = time();
                    
                    // Set remember me cookie (30 days)
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + (86400 * 30), "/");
                        
                        // Store token in database
                        $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                        $stmt->bind_param("si", $token, $user['id']);
                        $stmt->execute();
                    }
                    
                    // Update last login
                    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $stmt->bind_param("i", $user['id']);
                    $stmt->execute();
                    
                    // Redirect to original page or home
                    $redirect = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'index.php';
                    unset($_SESSION['redirect_url']);
                    
                    $success = "Login successful! Redirecting...";
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = '$redirect';
                        }, 1500);
                    </script>";
                } else {
                    $error = "Invalid password. Please try again.";
                }
            } else {
                $error = "User not found. Please check your username/email or <a href='register.php' style='color: #2e7d32; font-weight: 600;'>register here</a>.";
            }
        }
    }
}

// Generate CSRF token
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Uchumi Grocery | Fresh Kenyan Produce</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* Login Page Specific Styles */
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #ff9800;
            --accent-color: #4caf50;
            --dark-color: #1b5e20;
            --light-color: #f1f8e9;
            --text-color: #333;
            --text-light: #666;
            --white: #ffffff;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .main-header {
            background: var(--white);
            box-shadow: var(--shadow);
            padding: 15px 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: var(--primary-color);
        }

        .logo-icon {
            background: var(--primary-color);
            color: var(--white);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .logo-text h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .tagline {
            color: var(--secondary-color);
            font-size: 14px;
            font-weight: 500;
        }

        /* Login Container */
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: var(--white);
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.1;
        }

        .login-left h1 {
            font-size: 36px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .login-left p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .features {
            margin-top: 40px;
            position: relative;
            z-index: 1;
        }

        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .feature-icon {
            background: rgba(255,255,255,0.2);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
            flex-shrink: 0;
        }

        .feature-text h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .feature-text p {
            font-size: 14px;
            margin: 0;
            opacity: 0.8;
        }

        .login-right {
            flex: 1;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 32px;
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .login-header p {
            color: var(--text-light);
        }

        /* Messages */
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }

        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        .message i {
            margin-right: 10px;
            font-size: 20px;
        }

        /* Form Styles */
        .login-form .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 500;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 18px;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
            background: var(--white);
        }

        .form-control.error {
            border-color: #c62828;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 18px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
        }

        .checkbox-container input {
            margin-right: 8px;
            width: 18px;
            height: 18px;
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--dark-color);
            text-decoration: underline;
        }

        /* Buttons */
        .btn {
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
        }

        .btn-secondary {
            background: #f5f5f5;
            color: var(--text-color);
            border: 2px solid #e0e0e0;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .btn-google {
            background: #ffffff;
            color: #333;
            border: 2px solid #ddd;
            margin-top: 15px;
        }

        .btn-google:hover {
            background: #f5f5f5;
            border-color: #ccc;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 30px;
            color: var(--text-light);
        }

        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-footer a:hover {
            color: var(--dark-color);
            text-decoration: underline;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: var(--text-light);
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e0e0;
        }

        .divider span {
            padding: 0 15px;
            font-size: 14px;
        }

        /* Register Prompt */
        .register-prompt {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border: 2px dashed var(--primary-color);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-top: 30px;
            animation: pulseBorder 2s infinite;
        }

        @keyframes pulseBorder {
            0% { border-color: var(--primary-color); }
            50% { border-color: var(--accent-color); }
            100% { border-color: var(--primary-color); }
        }

        .register-prompt h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 18px;
        }

        .register-prompt p {
            color: var(--text-light);
            margin-bottom: 15px;
            font-size: 14px;
        }

        .register-prompt .btn {
            background: var(--secondary-color);
            color: var(--white);
            width: auto;
            padding: 12px 25px;
            margin: 0 auto;
        }

        .register-prompt .btn:hover {
            background: #e68900;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 500px;
            }

            .login-left {
                padding: 40px 30px;
            }

            .login-right {
                padding: 40px 30px;
            }

            .login-left h1 {
                font-size: 28px;
            }

            .login-header h2 {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .login-left,
            .login-right {
                padding: 30px 20px;
            }

            .feature {
                flex-direction: column;
                text-align: center;
            }

            .feature-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }

            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }

        /* Loading animation */
        .btn.loading {
            position: relative;
            color: transparent;
        }

        .btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: var(--white);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Footer */
        .main-footer {
            background: #2c3e50;
            color: var(--white);
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }

        .footer-links {
            margin-top: 10px;
        }

        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }

        .footer-links a:hover {
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <div class="logo-text">
                        <h1>Uchumi Grocery</h1>
                        <p class="tagline">Fresh Kenyan Produce</p>
                    </div>
                </a>
                <a href="index.php" class="btn-secondary" style="width: auto; padding: 10px 20px;">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="login-container">
        <div class="login-wrapper">
            <!-- Left Side - Information -->
            <div class="login-left">
                <h1>Welcome Back!</h1>
                <p>Login to access exclusive deals and fresh Kenyan produce delivered to your doorstep in Kahawa Wendani.</p>
                
                <div class="features">
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Fast Delivery</h3>
                            <p>Same day delivery in Kahawa Wendani</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Fresh Produce</h3>
                            <p>Direct from Kenyan farms</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Secure Shopping</h3>
                            <p>Your data is protected</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="login-right">
                <div class="login-header">
                    <h2>Login to Your Account</h2>
                    <p>Enter your credentials to continue</p>
                </div>

                <!-- Messages -->
                <?php if (!empty($error)): ?>
                    <div class="message error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="message success-message">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="post" action="login.php" class="login-form" id="loginForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label for="username" class="form-label">Username or Email</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   class="form-control" 
                                   placeholder="Enter your username or email" 
                                   value="<?php echo htmlspecialchars($username); ?>"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control" 
                                   placeholder="Enter your password" 
                                   required>
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="remember-forgot">
                        <div class="checkbox-container">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="forgot_password.php" class="forgot-password">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary" id="loginBtn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>

                    <div class="divider">
                        <span>Or continue with</span>
                    </div>

                    <button type="button" class="btn btn-google" onclick="alert('Google login coming soon!')">
                        <i class="fab fa-google"></i> Google
                    </button>
                </form>

                <!-- Register Prompt -->
                <div class="register-prompt">
                    <h3>New to Uchumi Grocery?</h3>
                    <p>Join our community and enjoy fresh Kenyan produce delivered to your doorstep.</p>
                    <a href="register.php" class="btn">
                        <i class="fas fa-user-plus"></i> Create Account
                    </a>
                </div>

                <div class="login-footer">
                    <p>Don't have an account? <a href="register.php">Create one here</a></p>
                    <p>By logging in, you agree to our <a href="terms.php">Terms</a> and <a href="privacy.php">Privacy Policy</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Uchumi Grocery Store, Kahawa Wendani. All rights reserved.</p>
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="about.php">About Us</a>
                <a href="contact.php">Contact</a>
                <a href="terms.php">Terms</a>
                <a href="privacy.php">Privacy</a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                });
            }

            // Form validation
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    const username = document.getElementById('username').value.trim();
                    const password = document.getElementById('password').value.trim();
                    
                    if (!username || !password) {
                        e.preventDefault();
                        alert('Please fill in all fields');
                        return;
                    }
                    
                    // Show loading state
                    if (loginBtn) {
                        loginBtn.classList.add('loading');
                        loginBtn.disabled = true;
                    }
                });
            }

            // Auto-focus username field
            const usernameField = document.getElementById('username');
            if (usernameField) {
                usernameField.focus();
            }

            // Check for URL parameters for messages
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('registered')) {
                const successDiv = document.createElement('div');
                successDiv.className = 'message success-message';
                successDiv.innerHTML = '<i class="fas fa-check-circle"></i><span>Registration successful! Please login with your credentials.</span>';
                loginForm.insertBefore(successDiv, loginForm.firstChild);
            }

            if (urlParams.has('logout')) {
                const successDiv = document.createElement('div');
                successDiv.className = 'message success-message';
                successDiv.innerHTML = '<i class="fas fa-check-circle"></i><span>You have been successfully logged out.</span>';
                loginForm.insertBefore(successDiv, loginForm.firstChild);
            }

            // Add animation to register prompt
            const registerPrompt = document.querySelector('.register-prompt');
            if (registerPrompt) {
                registerPrompt.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
                });
                
                registerPrompt.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            }
        });
    </script>
</body>
</html>