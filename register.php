<?php
// Add enhanced error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

// Check if config.php loaded properly
if (!isset($conn)) {
    die("❌ config.php not loaded properly - connection not established");
}

// Debug: Check if we can access the database
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';
$debug_info = ''; // Store debug information

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $debug_info .= "<!-- Form submitted via POST -->\n";
    
    // Debug: Show POST data (excluding password for security)
    $debug_post = $_POST;
    unset($debug_post['password']);
    unset($debug_post['confirm_password']);
    $debug_info .= "<!-- POST data: " . htmlspecialchars(print_r($debug_post, true)) . " -->\n";
    
    // Check if CSRF token exists in session
    if (!isset($_SESSION['csrf_token'])) {
        $debug_info .= "<!-- No CSRF token in session -->\n";
    } else {
        $debug_info .= "<!-- CSRF token in session exists -->\n";
    }
    
    // Validate CSRF token
    if (!isset($_POST['csrf_token'])) {
        $error = "Security token missing. Please try again.";
        $debug_info .= "<!-- CSRF token not in POST -->\n";
    } elseif (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = "Security token invalid. Please try again.";
        $debug_info .= "<!-- CSRF token verification failed -->\n";
    } else {
        $debug_info .= "<!-- CSRF token verified -->\n";
        
        // Get form data
        $username = sanitizeInput($_POST['username']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $full_name = sanitizeInput($_POST['full_name']);
        $phone = sanitizeInput($_POST['phone']);
        $address = sanitizeInput($_POST['address']);
        
        $debug_info .= "<!-- Username: $username -->\n";
        $debug_info .= "<!-- Email: $email -->\n";
        $debug_info .= "<!-- Full Name: $full_name -->\n";
        $debug_info .= "<!-- Phone (raw): $phone -->\n";
        $debug_info .= "<!-- Address: $address -->\n";
        
        // Clean phone number - remove all non-digit characters for validation
        $phone_clean = preg_replace('/\D/', '', $phone);
        
        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $error = "Please fill in all required fields (username, email, password)";
            $debug_info .= "<!-- Validation: Required fields empty -->\n";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address";
            $debug_info .= "<!-- Validation: Invalid email -->\n";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match";
            $debug_info .= "<!-- Validation: Passwords don't match -->\n";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters long";
            $debug_info .= "<!-- Validation: Password too short -->\n";
        } elseif (!empty($phone_clean) && (strlen($phone_clean) < 10 || strlen($phone_clean) > 15)) {
            $error = "Please enter a valid phone number (10-15 digits)";
            $debug_info .= "<!-- Validation: Invalid phone length -->\n";
        } else {
            $debug_info .= "<!-- Validation passed -->\n";
            
            // Use cleaned phone number (digits only) or empty string
            $phone = !empty($phone_clean) ? $phone_clean : '';
            
            // Check if username or email already exists
            $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
            $debug_info .= "<!-- Check SQL: $check_sql -->\n";
            
            if ($stmt = $conn->prepare($check_sql)) {
                $stmt->bind_param("ss", $username, $email);
                $stmt->execute();
                $stmt->store_result();
                
                $debug_info .= "<!-- Check query executed, rows found: " . $stmt->num_rows . " -->\n";
                
                if ($stmt->num_rows > 0) {
                    $error = "Username or email already exists. Please <a href='login.php' style='color: #2e7d32; font-weight: 600;'>login here</a>.";
                } else {
                    $stmt->close();
                    
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $debug_info .= "<!-- Password hashed -->\n";
                    
                    // Insert user
                    $insert_sql = "INSERT INTO users (username, email, password, full_name, phone, address) VALUES (?, ?, ?, ?, ?, ?)";
                    $debug_info .= "<!-- Insert SQL: $insert_sql -->\n";
                    
                    if ($stmt = $conn->prepare($insert_sql)) {
                        $stmt->bind_param("ssssss", $username, $email, $hashed_password, $full_name, $phone, $address);
                        
                        if ($stmt->execute()) {
                            $debug_info .= "<!-- Execute successful -->\n";
                            
                            // Get the new user ID
                            $user_id = $stmt->insert_id;
                            $debug_info .= "<!-- New user ID: $user_id -->\n";
                            
                            // Auto-login after registration
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['username'] = $username;
                            $_SESSION['full_name'] = $full_name;
                            $_SESSION['email'] = $email;
                            $_SESSION['login_time'] = time();
                            
                            $debug_info .= "<!-- Session variables set -->\n";
                            
                            // Redirect immediately
                            $debug_info .= "<!-- Redirecting to index.php -->\n";
                            header("Location: index.php?registration=success");
                            exit();
                        } else {
                            $error = "Registration failed: " . $stmt->error;
                            $debug_info .= "<!-- Execute failed: " . $stmt->error . " -->\n";
                        }
                    } else {
                        $error = "Database error. Please try again.";
                        $debug_info .= "<!-- Prepare failed: " . $conn->error . " -->\n";
                    }
                }
                $stmt->close();
            } else {
                $error = "Database error. Please try again.";
                $debug_info .= "<!-- Check prepare failed: " . $conn->error . " -->\n";
            }
        }
    }
}

$csrf_token = generateCSRFToken();
$debug_info .= "<!-- Generated CSRF token: " . substr($csrf_token, 0, 10) . "... -->\n";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Uchumi Grocery | Fresh Kenyan Produce</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* Register Page Specific Styles */
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

        /* Register Container */
        .register-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .register-wrapper {
            display: flex;
            width: 100%;
            max-width: 1100px;
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .register-left {
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

        .register-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.1;
        }

        .register-left h1 {
            font-size: 36px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .register-left p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .benefits {
            margin-top: 40px;
            position: relative;
            z-index: 1;
        }

        .benefit {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .benefit-icon {
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

        .benefit-text h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .benefit-text p {
            font-size: 14px;
            margin: 0;
            opacity: 0.8;
        }

        .welcome-offer {
            background: rgba(255,255,255,0.1);
            border: 2px dashed rgba(255,255,255,0.3);
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            position: relative;
            z-index: 1;
            text-align: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { border-color: rgba(255,255,255,0.3); }
            50% { border-color: rgba(255,255,255,0.6); }
            100% { border-color: rgba(255,255,255,0.3); }
        }

        .welcome-offer h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #ffcc80;
        }

        .welcome-offer p {
            font-size: 14px;
            opacity: 0.9;
        }

        .register-right {
            flex: 1.2;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-header {
            margin-bottom: 40px;
        }

        .register-header h2 {
            font-size: 32px;
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .register-header p {
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
        .register-form .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 20px;
            }
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 500;
            font-size: 14px;
        }

        .form-label .required {
            color: #e53935;
        }

        .form-label .optional {
            color: #757575;
            font-weight: normal;
            font-size: 12px;
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

        .form-control.success {
            border-color: #4caf50;
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

        /* Password Strength Indicator */
        .password-strength {
            height: 5px;
            background: #eee;
            border-radius: 5px;
            margin-top: 8px;
            overflow: hidden;
        }

        .strength-meter {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
        }

        .strength-weak { 
            background: #ff4757; 
            width: 25%;
        }
        .strength-medium { 
            background: #ffa502; 
            width: 50%;
        }
        .strength-good { 
            background: #2ed573; 
            width: 75%;
        }
        .strength-strong { 
            background: #2e7d32; 
            width: 100%;
        }

        .strength-text {
            font-size: 12px;
            margin-top: 5px;
            text-align: right;
            color: var(--text-light);
        }

        /* Password Requirements */
        .password-requirements {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            font-size: 13px;
            color: var(--text-light);
        }

        .password-requirements h4 {
            margin-bottom: 8px;
            color: var(--text-color);
            font-size: 14px;
        }

        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .requirement i {
            margin-right: 8px;
            font-size: 12px;
        }

        .requirement.valid {
            color: #4caf50;
        }

        .requirement.invalid {
            color: #e53935;
        }

        /* Terms Checkbox */
        .terms-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }

        .terms-container label {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
            font-size: 14px;
            color: var(--text-color);
            line-height: 1.5;
        }

        .terms-container input[type="checkbox"] {
            margin-top: 3px;
            min-width: 18px;
            min-height: 18px;
            accent-color: var(--primary-color);
        }

        .terms-container a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .terms-container a:hover {
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
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46, 125, 50, 0.4);
        }

        .btn-secondary {
            background: #f5f5f5;
            color: var(--text-color);
            border: 2px solid #e0e0e0;
            width: auto;
            padding: 10px 20px;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        /* Login Prompt */
        .login-prompt {
            text-align: center;
            margin-top: 25px;
            color: var(--text-light);
            font-size: 14px;
        }

        .login-prompt a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
        }

        .login-prompt a:hover {
            text-decoration: underline;
        }

        /* Form Validation Indicators */
        .validation-indicator {
            position: absolute;
            right: 45px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
        }

        .valid-indicator {
            color: #4caf50;
        }

        .invalid-indicator {
            color: #e53935;
        }

        /* Progress Bar */
        .progress-bar {
            width: 100%;
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            width: 0%;
            transition: width 0.5s ease;
            border-radius: 2px;
        }

        .progress-text {
            text-align: center;
            font-size: 12px;
            color: var(--text-light);
            margin-bottom: 5px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .register-wrapper {
                flex-direction: column;
                max-width: 700px;
            }

            .register-left {
                padding: 40px 30px;
            }

            .register-right {
                padding: 40px 30px;
            }

            .register-left h1 {
                font-size: 28px;
            }

            .register-header h2 {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .register-left,
            .register-right {
                padding: 30px 20px;
            }

            .benefit {
                flex-direction: column;
                text-align: center;
            }

            .benefit-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }

            .form-control {
                padding: 12px 12px 12px 45px;
                font-size: 14px;
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

        /* Step Indicator (Optional - for multi-step forms) */
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 1;
        }

        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 33.33%;
        }

        .step-number {
            width: 32px;
            height: 32px;
            background: #e0e0e0;
            color: var(--text-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.2);
        }

        .step-label {
            font-size: 12px;
            color: var(--text-light);
        }

        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Debug information (hidden but viewable in page source) -->
    <div style="display: none;"><?php echo $debug_info; ?></div>
    
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
                <a href="login.php" class="btn-secondary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="register-container">
        <div class="register-wrapper">
            <!-- Left Side - Information -->
            <div class="register-left">
                <h1>Join Our Community!</h1>
                <p>Register now to enjoy fresh Kenyan produce delivered to your doorstep in Kahawa Wendani.</p>
                
                <div class="benefits">
                    <div class="benefit">
                        <div class="benefit-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="benefit-text">
                            <h3>Free Delivery</h3>
                            <p>For orders above KSh 1,000 in Kahawa Wendani</p>
                        </div>
                    </div>
                    <div class="benefit">
                        <div class="benefit-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div class="benefit-text">
                            <h3>Welcome Bonus</h3>
                            <p>Get KSh 200 off your first order</p>
                        </div>
                    </div>
                    <div class="benefit">
                        <div class="benefit-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="benefit-text">
                            <h3>Exclusive Deals</h3>
                            <p>Member-only discounts & seasonal offers</p>
                        </div>
                    </div>
                    <div class="benefit">
                        <div class="benefit-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="benefit-text">
                            <h3>Order History</h3>
                            <p>Track your orders and reorder easily</p>
                        </div>
                    </div>
                </div>

                <div class="welcome-offer">
                    <h3>Limited Time Offer!</h3>
                    <p>Register today and get <strong>KSh 200</strong> off your first purchase of KSh 1,000 or more.</p>
                </div>
            </div>

            <!-- Right Side - Registration Form -->
            <div class="register-right">
                <div class="register-header">
                    <h2>Create Your Account</h2>
                    <p>Fill in your details to get started with Uchumi Grocery</p>
                </div>

                <!-- Registration Progress Bar -->
                <div class="progress-text">Account Setup: <span id="progressPercent">0%</span> complete</div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>

                <!-- Messages -->
                <?php if ($error): ?>
                    <div class="message error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['registration']) && $_GET['registration'] == 'success'): ?>
                    <div class="message success-message">
                        <i class="fas fa-check-circle"></i>
                        <span>Registration successful! You are now logged in.</span>
                    </div>
                <?php endif; ?>

                <!-- Registration Form -->
                <form method="post" action="register.php" class="register-form" id="registerForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <h3 style="margin-bottom: 20px; color: var(--primary-color); font-size: 18px;">
                        <i class="fas fa-user-circle"></i> Personal Information
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username" class="form-label">Username <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="username" name="username" class="form-control" 
                                       placeholder="Choose a username" required
                                       oninput="updateProgress()">
                                <span class="validation-indicator" id="usernameIndicator"></span>
                            </div>
                            <small style="color: var(--text-light); font-size: 12px; display: block; margin-top: 5px;">
                                Minimum 3 characters, letters and numbers only
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="email" name="email" class="form-control" 
                                       placeholder="your.email@example.com" required
                                       oninput="updateProgress()">
                                <span class="validation-indicator" id="emailIndicator"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name" class="form-label">Full Name <span class="optional">(optional)</span></label>
                            <div class="input-with-icon">
                                <i class="fas fa-user-tag input-icon"></i>
                                <input type="text" id="full_name" name="full_name" class="form-control" 
                                       placeholder="Enter your full name"
                                       oninput="updateProgress()">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number <span class="optional">(optional)</span></label>
                            <div class="input-with-icon">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" id="phone" name="phone" class="form-control" 
                                       placeholder="0712 345 678"
                                       oninput="updateProgress()">
                                <span class="validation-indicator" id="phoneIndicator"></span>
                            </div>
                            <small style="color: var(--text-light); font-size: 12px; display: block; margin-top: 5px;">
                                Optional - for delivery updates
                            </small>
                        </div>
                    </div>

                    <h3 style="margin: 30px 0 20px; color: var(--primary-color); font-size: 18px;">
                        <i class="fas fa-lock"></i> Security Information
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password" class="form-label">Password <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="password" name="password" class="form-control" 
                                       placeholder="Create a strong password" required
                                       oninput="checkPasswordStrength()">
                                <button type="button" class="password-toggle" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <span class="validation-indicator" id="passwordIndicator"></span>
                            </div>
                            
                            <div class="password-strength">
                                <div class="strength-meter" id="strengthMeter"></div>
                            </div>
                            <div class="strength-text" id="strengthText">Password strength: None</div>
                            
                            <div class="password-requirements">
                                <h4>Password Requirements:</h4>
                                <div class="requirement" id="reqLength">
                                    <i class="fas fa-circle"></i>
                                    <span>At least 6 characters</span>
                                </div>
                                <div class="requirement" id="reqUppercase">
                                    <i class="fas fa-circle"></i>
                                    <span>Contains uppercase letter</span>
                                </div>
                                <div class="requirement" id="reqNumber">
                                    <i class="fas fa-circle"></i>
                                    <span>Contains number</span>
                                </div>
                                <div class="requirement" id="reqSpecial">
                                    <i class="fas fa-circle"></i>
                                    <span>Contains special character</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm Password <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                                       placeholder="Confirm your password" required
                                       oninput="checkPasswordMatch()">
                                <button type="button" class="password-toggle" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <span class="validation-indicator" id="confirmPasswordIndicator"></span>
                            </div>
                            <div id="passwordMatchMessage" style="font-size: 12px; margin-top: 5px;"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">Delivery Address <span class="optional">(optional)</span></label>
                        <div class="input-with-icon">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <textarea id="address" name="address" class="form-control" rows="3" 
                                      placeholder="Enter your delivery address in Kahawa Wendani"
                                      oninput="updateProgress()"></textarea>
                        </div>
                        <small style="color: var(--text-light); font-size: 12px; display: block; margin-top: 5px;">
                            We deliver within Kahawa Wendani and surrounding areas
                        </small>
                    </div>

                    <div class="terms-container">
                        <label>
                            <input type="checkbox" id="terms" name="terms" required onchange="updateProgress()">
                            <span>
                                I agree to the <a href="terms.php">Terms and Conditions</a> and 
                                <a href="privacy.php">Privacy Policy</a>. I confirm that I am at least 18 years old.
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" id="registerBtn">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>

                    <div class="login-prompt">
                        Already have an account? <a href="login.php">Login here</a>
                    </div>
                </form>
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
            // Toggle password visibility for both fields
            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            
            function setupPasswordToggle(button, input) {
                if (button && input) {
                    button.addEventListener('click', function() {
                        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                        input.setAttribute('type', type);
                        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                    });
                }
            }
            
            setupPasswordToggle(togglePassword, passwordInput);
            setupPasswordToggle(toggleConfirmPassword, confirmPasswordInput);
            
            // Initial password strength check
            checkPasswordStrength();
            checkPasswordMatch();
            updateProgress();
            
            // Form validation
            const registerForm = document.getElementById('registerForm');
            const registerBtn = document.getElementById('registerBtn');
            
            if (registerForm) {
                registerForm.addEventListener('submit', function(e) {
                    // Check required fields
                    const username = document.getElementById('username').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const password = document.getElementById('password').value.trim();
                    const confirmPassword = document.getElementById('confirm_password').value.trim();
                    const terms = document.getElementById('terms').checked;
                    
                    if (!username || !email || !password || !confirmPassword || !terms) {
                        e.preventDefault();
                        alert('Please fill in all required fields and accept the terms.');
                        return;
                    }
                    
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Passwords do not match.');
                        return;
                    }
                    
                    // Show loading state
                    if (registerBtn) {
                        registerBtn.classList.add('loading');
                        registerBtn.disabled = true;
                        registerBtn.innerHTML = '<i class="fas fa-spinner"></i> Creating Account...';
                    }
                });
            }
            
            // Real-time username validation
            const usernameInput = document.getElementById('username');
            const usernameIndicator = document.getElementById('usernameIndicator');
            
            if (usernameInput && usernameIndicator) {
                usernameInput.addEventListener('input', function() {
                    const username = this.value.trim();
                    if (username.length < 3) {
                        usernameIndicator.innerHTML = '<i class="fas fa-times invalid-indicator"></i>';
                        usernameIndicator.title = 'Username must be at least 3 characters';
                        this.classList.add('error');
                        this.classList.remove('success');
                    } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                        usernameIndicator.innerHTML = '<i class="fas fa-times invalid-indicator"></i>';
                        usernameIndicator.title = 'Only letters, numbers, and underscores allowed';
                        this.classList.add('error');
                        this.classList.remove('success');
                    } else {
                        usernameIndicator.innerHTML = '<i class="fas fa-check valid-indicator"></i>';
                        usernameIndicator.title = 'Username is valid';
                        this.classList.remove('error');
                        this.classList.add('success');
                    }
                });
            }
            
            // Email validation
            const emailInput = document.getElementById('email');
            const emailIndicator = document.getElementById('emailIndicator');
            
            if (emailInput && emailIndicator) {
                emailInput.addEventListener('input', function() {
                    const email = this.value.trim();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    
                    if (emailRegex.test(email)) {
                        emailIndicator.innerHTML = '<i class="fas fa-check valid-indicator"></i>';
                        emailIndicator.title = 'Email is valid';
                        this.classList.remove('error');
                        this.classList.add('success');
                    } else if (email.length > 0) {
                        emailIndicator.innerHTML = '<i class="fas fa-times invalid-indicator"></i>';
                        emailIndicator.title = 'Please enter a valid email address';
                        this.classList.add('error');
                        this.classList.remove('success');
                    } else {
                        emailIndicator.innerHTML = '';
                        this.classList.remove('error', 'success');
                    }
                });
            }
            
            // Phone validation - Optional field
            const phoneInput = document.getElementById('phone');
            const phoneIndicator = document.getElementById('phoneIndicator');
            
            if (phoneInput && phoneIndicator) {
                phoneInput.addEventListener('input', function() {
                    const phone = this.value.replace(/\D/g, ''); // Remove non-digits
                    
                    if (phone.length === 0) {
                        // Empty is OK (optional field)
                        phoneIndicator.innerHTML = '';
                        this.classList.remove('error', 'success');
                    } else if (phone.length >= 10 && phone.length <= 15) {
                        phoneIndicator.innerHTML = '<i class="fas fa-check valid-indicator"></i>';
                        phoneIndicator.title = 'Phone number is valid';
                        this.classList.remove('error');
                        this.classList.add('success');
                    } else {
                        phoneIndicator.innerHTML = '<i class="fas fa-times invalid-indicator"></i>';
                        phoneIndicator.title = 'Phone number should be 10-15 digits';
                        this.classList.add('error');
                        this.classList.remove('success');
                    }
                    
                    // Format phone number as user types (optional)
                    if (phone.length > 0) {
                        let formatted = '';
                        if (phone.length <= 3) {
                            formatted = phone;
                        } else if (phone.length <= 6) {
                            formatted = phone.substring(0, 3) + ' ' + phone.substring(3);
                        } else if (phone.length <= 9) {
                            formatted = phone.substring(0, 3) + ' ' + phone.substring(3, 6) + ' ' + phone.substring(6);
                        } else {
                            formatted = phone.substring(0, 3) + ' ' + phone.substring(3, 6) + ' ' + phone.substring(6, 9) + ' ' + phone.substring(9, 12);
                        }
                        this.value = formatted;
                    }
                });
            }
        });
        
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthMeter = document.getElementById('strengthMeter');
            const strengthText = document.getElementById('strengthText');
            const passwordIndicator = document.getElementById('passwordIndicator');
            
            // Reset requirements
            const reqLength = document.getElementById('reqLength');
            const reqUppercase = document.getElementById('reqUppercase');
            const reqNumber = document.getElementById('reqNumber');
            const reqSpecial = document.getElementById('reqSpecial');
            
            let strength = 0;
            let requirements = [];
            
            // Check length
            if (password.length >= 6) {
                strength += 25;
                reqLength.classList.remove('invalid');
                reqLength.classList.add('valid');
                reqLength.innerHTML = '<i class="fas fa-check-circle"></i><span>At least 6 characters ✓</span>';
            } else {
                reqLength.classList.remove('valid');
                reqLength.classList.add('invalid');
                reqLength.innerHTML = '<i class="fas fa-circle"></i><span>At least 6 characters</span>';
            }
            
            // Check uppercase
            if (/[A-Z]/.test(password)) {
                strength += 25;
                reqUppercase.classList.remove('invalid');
                reqUppercase.classList.add('valid');
                reqUppercase.innerHTML = '<i class="fas fa-check-circle"></i><span>Contains uppercase letter ✓</span>';
            } else {
                reqUppercase.classList.remove('valid');
                reqUppercase.classList.add('invalid');
                reqUppercase.innerHTML = '<i class="fas fa-circle"></i><span>Contains uppercase letter</span>';
            }
            
            // Check number
            if (/[0-9]/.test(password)) {
                strength += 25;
                reqNumber.classList.remove('invalid');
                reqNumber.classList.add('valid');
                reqNumber.innerHTML = '<i class="fas fa-check-circle"></i><span>Contains number ✓</span>';
            } else {
                reqNumber.classList.remove('valid');
                reqNumber.classList.add('invalid');
                reqNumber.innerHTML = '<i class="fas fa-circle"></i><span>Contains number</span>';
            }
            
            // Check special character
            if (/[^A-Za-z0-9]/.test(password)) {
                strength += 25;
                reqSpecial.classList.remove('invalid');
                reqSpecial.classList.add('valid');
                reqSpecial.innerHTML = '<i class="fas fa-check-circle"></i><span>Contains special character ✓</span>';
            } else {
                reqSpecial.classList.remove('valid');
                reqSpecial.classList.add('invalid');
                reqSpecial.innerHTML = '<i class="fas fa-circle"></i><span>Contains special character</span>';
            }
            
            // Update strength meter
            strengthMeter.className = 'strength-meter';
            strengthMeter.style.width = strength + '%';
            
            // Update strength text and color
            if (strength === 0) {
                strengthText.textContent = 'Password strength: None';
                strengthMeter.style.backgroundColor = '#eee';
            } else if (strength <= 25) {
                strengthText.textContent = 'Password strength: Weak';
                strengthMeter.classList.add('strength-weak');
            } else if (strength <= 50) {
                strengthText.textContent = 'Password strength: Fair';
                strengthMeter.classList.add('strength-medium');
            } else if (strength <= 75) {
                strengthText.textContent = 'Password strength: Good';
                strengthMeter.classList.add('strength-good');
            } else {
                strengthText.textContent = 'Password strength: Strong';
                strengthMeter.classList.add('strength-strong');
            }
            
            // Update indicator
            if (password.length === 0) {
                passwordIndicator.innerHTML = '';
                passwordIndicator.title = '';
            } else if (strength >= 75) {
                passwordIndicator.innerHTML = '<i class="fas fa-check valid-indicator"></i>';
                passwordIndicator.title = 'Strong password';
            } else if (strength >= 50) {
                passwordIndicator.innerHTML = '<i class="fas fa-exclamation-triangle" style="color: #ffa502;"></i>';
                passwordIndicator.title = 'Could be stronger';
            } else {
                passwordIndicator.innerHTML = '<i class="fas fa-times invalid-indicator"></i>';
                passwordIndicator.title = 'Weak password';
            }
            
            updateProgress();
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchMessage = document.getElementById('passwordMatchMessage');
            const confirmPasswordIndicator = document.getElementById('confirmPasswordIndicator');
            
            if (confirmPassword.length === 0) {
                matchMessage.textContent = '';
                matchMessage.style.color = '';
                confirmPasswordIndicator.innerHTML = '';
            } else if (password === confirmPassword) {
                matchMessage.textContent = 'Passwords match ✓';
                matchMessage.style.color = '#4caf50';
                confirmPasswordIndicator.innerHTML = '<i class="fas fa-check valid-indicator"></i>';
                confirmPasswordIndicator.title = 'Passwords match';
            } else {
                matchMessage.textContent = 'Passwords do not match ✗';
                matchMessage.style.color = '#e53935';
                confirmPasswordIndicator.innerHTML = '<i class="fas fa-times invalid-indicator"></i>';
                confirmPasswordIndicator.title = 'Passwords do not match';
            }
            
            updateProgress();
        }
        
        function updateProgress() {
            const progressFill = document.getElementById('progressFill');
            const progressPercent = document.getElementById('progressPercent');
            
            let progress = 0;
            let filledFields = 0;
            let totalFields = 6; // Required fields only: username, email, password, confirm_password, terms
            
            // Check required fields
            if (document.getElementById('username').value.trim().length >= 3) filledFields++;
            if (document.getElementById('email').value.trim().length > 0) filledFields++;
            if (document.getElementById('password').value.length >= 6) filledFields++;
            if (document.getElementById('confirm_password').value.length > 0) filledFields++;
            if (document.getElementById('terms').checked) filledFields++;
            
            // Optional fields don't count toward required progress
            
            progress = Math.round((filledFields / totalFields) * 100);
            
            progressFill.style.width = progress + '%';
            progressPercent.textContent = progress + '%';
            
            // Color coding for progress
            if (progress < 33) {
                progressFill.style.background = '#ff4757';
            } else if (progress < 66) {
                progressFill.style.background = '#ffa502';
            } else if (progress < 100) {
                progressFill.style.background = '#2ed573';
            } else {
                progressFill.style.background = '#2e7d32';
            }
        }
    </script>
</body>
</html>