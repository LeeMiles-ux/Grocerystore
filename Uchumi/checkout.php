<?php
include 'config.php';

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Calculate totals
$subtotal = 0;
$delivery_fee = 0;
$tax_rate = 0.16; // 16% VAT
$items_count = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $items_count += $item['quantity'];
}

// Calculate delivery fee (free for orders above 1000)
$delivery_fee = $subtotal >= 1000 ? 0 : 200;
$tax_amount = $subtotal * $tax_rate;
$grand_total = $subtotal + $delivery_fee + $tax_amount;

// Get user information if logged in
$user_name = '';
$user_phone = '';
$user_address = '';

if (isLoggedIn()) {
    $user_name = $_SESSION['full_name'] ?? $_SESSION['username'] ?? '';
    // In a real app, you would fetch user details from database
    $user_phone = '0712 345 678'; // Sample
    $user_address = 'Kahawa Wendani, Nairobi'; // Sample
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Uchumi Grocery</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary-green: #2e7d32;
            --dark-green: #1b5e20;
            --light-green: #4caf50;
            --accent-orange: #ff9800;
            --accent-red: #e53935;
            --light-bg: #f9f9f9;
            --dark-text: #333333;
            --light-text: #666666;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --shadow-light: 0 5px 15px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark-text);
            background: var(--light-bg);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .announcement-bar {
            background: linear-gradient(90deg, var(--primary-green), var(--light-green));
            color: var(--white);
            padding: 12px 0;
            font-size: 14px;
        }

        .announcement-bar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .announcement-text {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .offer-tag {
            background: var(--accent-red);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .main-header {
            background: var(--white);
            box-shadow: var(--shadow-light);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo i {
            font-size: 28px;
            color: var(--white);
        }

        .logo-text h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .action-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--dark-text);
            position: relative;
            transition: var(--transition);
        }

        .action-item:hover {
            color: var(--primary-green);
        }

        .action-icon {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--accent-red);
            color: var(--white);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Checkout Page */
        .checkout-page {
            padding: 60px 0;
            min-height: 60vh;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-green);
            position: relative;
            padding-bottom: 15px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--accent-orange);
            border-radius: 2px;
        }

        .page-subtitle {
            color: var(--light-text);
            font-size: 16px;
            margin-top: 10px;
        }

        /* Checkout Layout */
        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
        }

        @media (max-width: 992px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }

        /* Checkout Steps */
        .checkout-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            position: relative;
        }

        .checkout-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            background: var(--white);
            padding: 0 10px;
        }

        .step-icon {
            width: 40px;
            height: 40px;
            background: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--light-text);
            transition: var(--transition);
        }

        .step.active .step-icon {
            background: var(--primary-green);
            color: var(--white);
            transform: scale(1.1);
        }

        .step.completed .step-icon {
            background: var(--light-green);
            color: var(--white);
        }

        .step-text {
            font-size: 14px;
            color: var(--light-text);
            font-weight: 500;
            text-align: center;
        }

        .step.active .step-text {
            color: var(--primary-green);
            font-weight: 600;
        }

        /* Form Styles */
        .checkout-form-section {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .checkout-form-section:hover {
            box-shadow: var(--shadow);
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-green);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-text);
        }

        .form-label .required {
            color: var(--accent-red);
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
            background: #f8f9fa;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-green);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .form-input.error {
            border-color: var(--accent-red);
            background: #fff5f5;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-helper {
            font-size: 13px;
            color: var(--light-text);
            margin-top: 5px;
        }

        /* Payment Methods */
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: var(--transition);
            background: #f8f9fa;
        }

        .payment-method:hover {
            border-color: var(--primary-green);
            background: #f0f9f0;
        }

        .payment-method.active {
            border-color: var(--primary-green);
            background: #f0f9f0;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .payment-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 24px;
        }

        .payment-info {
            flex: 1;
        }

        .payment-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .payment-description {
            font-size: 14px;
            color: var(--light-text);
        }

        /* Card Details (Hidden by default) */
        .card-details {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .card-details.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Order Summary */
        .order-summary {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow-light);
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .summary-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 25px;
            color: var(--dark-text);
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .summary-items {
            margin-bottom: 25px;
            max-height: 300px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--light-green), var(--primary-green));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .item-meta {
            font-size: 13px;
            color: var(--light-text);
        }

        .item-price {
            font-weight: 600;
            color: var(--primary-green);
            font-size: 14px;
        }

        .summary-totals {
            border-top: 2px solid #f0f0f0;
            padding-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .total-row.grand-total {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-green);
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .total-label {
            color: var(--light-text);
        }

        .total-value {
            font-weight: 600;
        }

        .delivery-info {
            background: #f0f9f0;
            padding: 20px;
            border-radius: 10px;
            margin-top: 25px;
            border-left: 4px solid var(--primary-green);
        }

        .delivery-info i {
            color: var(--primary-green);
            margin-right: 10px;
        }

        .delivery-info p {
            font-size: 14px;
            color: var(--dark-text);
        }

        /* Action Buttons */
        .checkout-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #f0f0f0;
        }

        .btn {
            padding: 18px 40px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-secondary {
            background: transparent;
            color: var(--primary-green);
            border: 2px solid var(--primary-green);
        }

        .btn-secondary:hover {
            background: var(--primary-green);
            color: var(--white);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: var(--white);
            box-shadow: 0 10px 20px rgba(46, 125, 50, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--dark-green), var(--primary-green));
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(46, 125, 50, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Security Badge */
        .security-badge {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .security-icon {
            color: var(--primary-green);
            font-size: 24px;
        }

        .security-text {
            flex: 1;
        }

        .security-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .security-description {
            font-size: 14px;
            color: var(--light-text);
        }

        /* Footer */
        .main-footer {
            background: #1a1a1a;
            color: var(--white);
            padding: 60px 0 30px;
            margin-top: 60px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-bottom {
            padding-top: 30px;
            border-top: 1px solid #333;
            text-align: center;
            color: #999;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-title {
                font-size: 28px;
            }
            
            .checkout-steps {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
            
            .checkout-steps::before {
                display: none;
            }
            
            .step {
                flex-direction: row;
                align-items: center;
                gap: 15px;
                width: 100%;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 10px;
            }
            
            .step-icon {
                margin-bottom: 0;
            }
            
            .checkout-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Success Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background: var(--white);
            border-radius: 20px;
            padding: 50px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 40px;
            color: var(--white);
            animation: scaleIn 0.5s ease 0.3s both;
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .modal-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 15px;
        }

        .modal-text {
            color: var(--light-text);
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--light-text);
            cursor: pointer;
            transition: var(--transition);
        }

        .close-modal:hover {
            color: var(--primary-green);
        }
    </style>
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <div class="container">
            <div class="announcement-text">
                <i class="fas fa-truck"></i>
                <span>Free delivery in Kahawa Wendani for orders above KSh 1,000</span>
                <span class="offer-tag">NEW OFFER</span>
            </div>
            <div class="announcement-text">
                <i class="fas fa-phone"></i>
                <span>Call us: 0712 345 678</span>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-container">
                <!-- Logo -->
                <div class="logo-section">
                    <div class="logo">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <div class="logo-text">
                        <h1>Uchumi Grocery</h1>
                        <p class="logo-tagline" style="color: var(--accent-orange); font-size: 14px; font-weight: 500; letter-spacing: 1px;">FRESH KENYAN PRODUCE</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="header-actions">
                    <a href="index.php" class="action-item">
                        <i class="fas fa-home action-icon"></i>
                        <span class="action-text">Home</span>
                    </a>
                    <a href="product.php" class="action-item">
                        <i class="fas fa-store action-icon"></i>
                        <span class="action-text">Shop</span>
                    </a>
                    <a href="cart.php" class="action-item">
                        <i class="fas fa-shopping-cart action-icon"></i>
                        <span class="action-text">Cart</span>
                        <span class="cart-count"><?php echo $items_count; ?></span>
                    </a>
                    <a href="order.php" class="action-item">
                        <i class="fas fa-box action-icon"></i>
                        <span class="action-text">Orders</span>
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="logout.php" class="action-item">
                            <i class="fas fa-sign-out-alt action-icon"></i>
                            <span class="action-text">Logout</span>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="action-item">
                            <i class="fas fa-user action-icon"></i>
                            <span class="action-text">Login</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Checkout Page -->
    <section class="checkout-page">
        <div class="container">
            <div class="page-header animate__animated animate__fadeIn">
                <h1 class="page-title">Checkout</h1>
                <p class="page-subtitle">Complete your order in just a few steps</p>
            </div>

            <!-- Checkout Steps -->
            <div class="checkout-steps animate__animated animate__fadeIn">
                <div class="step active">
                    <div class="step-icon">1</div>
                    <div class="step-text">Delivery</div>
                </div>
                <div class="step">
                    <div class="step-icon">2</div>
                    <div class="step-text">Payment</div>
                </div>
                <div class="step">
                    <div class="step-icon">3</div>
                    <div class="step-text">Review</div>
                </div>
                <div class="step">
                    <div class="step-icon">4</div>
                    <div class="step-text">Complete</div>
                </div>
            </div>

            <div class="checkout-container">
                <!-- Left Column - Checkout Form -->
                <div class="checkout-form">
                    <!-- Delivery Information -->
                    <div class="checkout-form-section animate__animated animate__fadeIn">
                        <h3 class="section-title">
                            <i class="fas fa-user"></i>
                            Customer Information
                        </h3>
                        
                        <div class="form-group">
                            <label class="form-label">Full Name <span class="required">*</span></label>
                            <input type="text" 
                                   class="form-input" 
                                   id="customerName" 
                                   placeholder="Enter your full name"
                                   value="<?php echo htmlspecialchars($user_name); ?>"
                                   required>
                            <div class="form-helper">As it appears on your ID</div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Phone Number <span class="required">*</span></label>
                                <input type="tel" 
                                       class="form-input" 
                                       id="customerPhone" 
                                       placeholder="0712 345 678"
                                       value="<?php echo htmlspecialchars($user_phone); ?>"
                                       required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" 
                                       class="form-input" 
                                       id="customerEmail" 
                                       placeholder="your.email@example.com"
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Delivery Address <span class="required">*</span></label>
                            <textarea class="form-input form-textarea" 
                                      id="deliveryAddress" 
                                      placeholder="Enter complete delivery address including house number, street, and any landmarks"
                                      required><?php echo htmlspecialchars($user_address); ?></textarea>
                            <div class="form-helper">We deliver within Kahawa Wendani area only</div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Delivery Date <span class="required">*</span></label>
                                <select class="form-input" id="deliveryDate" required>
                                    <option value="">Select delivery date</option>
                                    <option value="today">Today (within 2 hours)</option>
                                    <option value="tomorrow">Tomorrow</option>
                                    <option value="custom">Choose specific date</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Delivery Time <span class="required">*</span></label>
                                <select class="form-input" id="deliveryTime" required>
                                    <option value="">Select time slot</option>
                                    <option value="morning">9:00 AM - 12:00 PM</option>
                                    <option value="afternoon">12:00 PM - 3:00 PM</option>
                                    <option value="evening">3:00 PM - 6:00 PM</option>
                                    <option value="night">6:00 PM - 9:00 PM</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Special Instructions (Optional)</label>
                            <textarea class="form-input form-textarea" 
                                      id="specialInstructions" 
                                      placeholder="Any special delivery instructions, e.g., leave at gate, call before delivery, etc."></textarea>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="checkout-form-section animate__animated animate__fadeIn" style="animation-delay: 0.2s;">
                        <h3 class="section-title">
                            <i class="fas fa-credit-card"></i>
                            Payment Method
                        </h3>
                        
                        <div class="payment-methods">
                            <div class="payment-method active" data-method="mpesa">
                                <div class="payment-icon">
                                    <i class="fab fa-mpesa"></i>
                                </div>
                                <div class="payment-info">
                                    <div class="payment-name">M-Pesa</div>
                                    <div class="payment-description">Pay instantly via M-Pesa</div>
                                </div>
                                <i class="fas fa-check-circle" style="color: var(--primary-green);"></i>
                            </div>

                            <div class="payment-method" data-method="cash">
                                <div class="payment-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="payment-info">
                                    <div class="payment-name">Cash on Delivery</div>
                                    <div class="payment-description">Pay when you receive your order</div>
                                </div>
                            </div>

                            <div class="payment-method" data-method="card">
                                <div class="payment-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="payment-info">
                                    <div class="payment-name">Credit/Debit Card</div>
                                    <div class="payment-description">Pay using Visa or MasterCard</div>
                                </div>
                            </div>
                        </div>

                        <!-- M-Pesa Details -->
                        <div class="card-details active" id="mpesa-details">
                            <div class="form-group">
                                <label class="form-label">M-Pesa Phone Number <span class="required">*</span></label>
                                <input type="tel" 
                                       class="form-input" 
                                       id="mpesaPhone" 
                                       placeholder="07XX XXX XXX"
                                       value="<?php echo htmlspecialchars($user_phone); ?>">
                                <div class="form-helper">You'll receive a prompt on this number</div>
                            </div>
                            <div class="security-badge">
                                <i class="fas fa-shield-alt security-icon"></i>
                                <div class="security-text">
                                    <div class="security-title">Secure M-Pesa Payment</div>
                                    <div class="security-description">Your payment is processed securely by Safaricom</div>
                                </div>
                            </div>
                        </div>

                        <!-- Cash on Delivery Details -->
                        <div class="card-details" id="cash-details">
                            <div class="delivery-info">
                                <i class="fas fa-info-circle"></i>
                                <p>Please have exact change ready. Our delivery agent will collect payment upon delivery.</p>
                            </div>
                        </div>

                        <!-- Card Details -->
                        <div class="card-details" id="card-details">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Card Number <span class="required">*</span></label>
                                    <input type="text" 
                                           class="form-input" 
                                           id="cardNumber" 
                                           placeholder="1234 5678 9012 3456"
                                           maxlength="19">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Card Holder <span class="required">*</span></label>
                                    <input type="text" 
                                           class="form-input" 
                                           id="cardHolder" 
                                           placeholder="Full Name">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Expiry Date <span class="required">*</span></label>
                                    <input type="text" 
                                           class="form-input" 
                                           id="cardExpiry" 
                                           placeholder="MM/YY"
                                           maxlength="5">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">CVV <span class="required">*</span></label>
                                    <input type="text" 
                                           class="form-input" 
                                           id="cardCVV" 
                                           placeholder="123"
                                           maxlength="3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Summary -->
                <div class="order-summary animate__animated animate__fadeIn" style="animation-delay: 0.4s;">
                    <h3 class="summary-title">Order Summary</h3>
                    
                    <div class="summary-items">
                        <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                            <div class="summary-item">
                                <div class="item-image">
                                    <i class="fas fa-carrot"></i>
                                </div>
                                <div class="item-details">
                                    <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="item-meta">Quantity: <?php echo $item['quantity']; ?> × KSh <?php echo number_format($item['price'], 2); ?></div>
                                </div>
                                <div class="item-price">
                                    KSh <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-totals">
                        <div class="total-row">
                            <span class="total-label">Subtotal</span>
                            <span class="total-value">KSh <?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        
                        <div class="total-row">
                            <span class="total-label">Delivery Fee</span>
                            <span class="total-value">
                                <?php if ($delivery_fee == 0): ?>
                                    <span style="color: var(--primary-green); font-weight: 600;">FREE</span>
                                <?php else: ?>
                                    KSh <?php echo number_format($delivery_fee, 2); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="total-row">
                            <span class="total-label">Tax (16% VAT)</span>
                            <span class="total-value">KSh <?php echo number_format($tax_amount, 2); ?></span>
                        </div>
                        
                        <div class="total-row grand-total">
                            <span class="total-label">Total Amount</span>
                            <span class="total-value">KSh <?php echo number_format($grand_total, 2); ?></span>
                        </div>
                    </div>

                    <?php if ($delivery_fee == 0): ?>
                        <div class="delivery-info">
                            <i class="fas fa-gift"></i>
                            <p>🎉 Congratulations! You qualify for FREE delivery!</p>
                        </div>
                    <?php else: ?>
                        <div class="delivery-info">
                            <i class="fas fa-truck"></i>
                            <p>Add KSh <?php echo number_format(1000 - $subtotal, 2); ?> more to qualify for FREE delivery!</p>
                        </div>
                    <?php endif; ?>

                    <div class="security-badge">
                        <i class="fas fa-lock security-icon"></i>
                        <div class="security-text">
                            <div class="security-title">Secure Checkout</div>
                            <div class="security-description">Your information is protected with 256-bit SSL encryption</div>
                        </div>
                    </div>

                    <div class="checkout-actions">
                        <a href="cart.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Cart
                        </a>
                        <button class="btn btn-primary" id="placeOrderBtn">
                            <i class="fas fa-shopping-bag"></i> Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-shopping-basket"></i>
                        <h2>Uchumi Grocery</h2>
                    </div>
                    <p class="footer-description">
                        Your trusted source for fresh Kenyan produce in Kahawa Wendani. 
                        Quality groceries delivered to your doorstep.
                    </p>
                </div>
                
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul class="footer-links" style="list-style: none;">
                        <li><a href="index.php" style="color: #999; text-decoration: none; transition: var(--transition); display: block; padding: 5px 0;">
                            <i class="fas fa-chevron-right" style="margin-right: 10px;"></i> Home
                        </a></li>
                        <li><a href="product.php" style="color: #999; text-decoration: none; transition: var(--transition); display: block; padding: 5px 0;">
                            <i class="fas fa-chevron-right" style="margin-right: 10px;"></i> Products
                        </a></li>
                        <li><a href="checkout.php" style="color: #999; text-decoration: none; transition: var(--transition); display: block; padding: 5px 0;">
                            <i class="fas fa-chevron-right" style="margin-right: 10px;"></i> Checkout
                        </a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Contact Info</h3>
                    <ul class="contact-info" style="list-style: none;">
                        <li style="margin-bottom: 15px;">
                            <i class="fas fa-map-marker-alt" style="color: var(--accent-orange); margin-right: 10px;"></i>
                            <span>Kahawa Wendani, Nairobi</span>
                        </li>
                        <li style="margin-bottom: 15px;">
                            <i class="fas fa-phone" style="color: var(--accent-orange); margin-right: 10px;"></i>
                            <span>0712 345 678</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Uchumi Grocery Store, Kahawa Wendani. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Success Modal -->
    <div class="modal" id="successModal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal()">×</button>
            <div class="modal-icon">
                <i class="fas fa-check"></i>
            </div>
            <h3 class="modal-title">Order Placed Successfully!</h3>
            <p class="modal-text" id="orderConfirmationText">
                Your order has been received and is being processed. 
                You will receive a confirmation SMS shortly.
            </p>
            <p class="modal-text" style="font-weight: 600; color: var(--primary-green);">
                Order Total: KSh <?php echo number_format($grand_total, 2); ?>
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
                <a href="order.php" class="btn btn-primary">
                    <i class="fas fa-box"></i> View Orders
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment method selection
            const paymentMethods = document.querySelectorAll('.payment-method');
            const cardDetails = document.querySelectorAll('.card-details');
            
            paymentMethods.forEach(method => {
                method.addEventListener('click', () => {
                    // Remove active class from all methods
                    paymentMethods.forEach(m => {
                        m.classList.remove('active');
                        m.querySelector('.fa-check-circle')?.remove();
                    });
                    
                    // Add active class to selected method
                    method.classList.add('active');
                    
                    // Add check icon
                    const checkIcon = document.createElement('i');
                    checkIcon.className = 'fas fa-check-circle';
                    checkIcon.style.color = 'var(--primary-green)';
                    method.appendChild(checkIcon);
                    
                    // Show corresponding details
                    const methodType = method.dataset.method;
                    cardDetails.forEach(detail => {
                        detail.classList.remove('active');
                        if (detail.id === `${methodType}-details`) {
                            detail.classList.add('active');
                        }
                    });
                });
            });
            
            // Card number formatting
            const cardNumberInput = document.getElementById('cardNumber');
            if (cardNumberInput) {
                cardNumberInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || '';
                    e.target.value = formattedValue;
                });
            }
            
            // Card expiry formatting
            const cardExpiryInput = document.getElementById('cardExpiry');
            if (cardExpiryInput) {
                cardExpiryInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/[^0-9]/g, '');
                    if (value.length >= 2) {
                        value = value.substring(0, 2) + '/' + value.substring(2, 4);
                    }
                    e.target.value = value;
                });
            }
            
            // Form validation
            const placeOrderBtn = document.getElementById('placeOrderBtn');
            const formInputs = document.querySelectorAll('.form-input[required]');
            
            function validateForm() {
                let isValid = true;
                
                formInputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('error');
                        isValid = false;
                    } else {
                        input.classList.remove('error');
                    }
                });
                
                // Validate email
                const emailInput = document.getElementById('customerEmail');
                if (emailInput && !isValidEmail(emailInput.value)) {
                    emailInput.classList.add('error');
                    isValid = false;
                }
                
                // Validate phone
                const phoneInput = document.getElementById('customerPhone');
                if (phoneInput && !isValidPhone(phoneInput.value)) {
                    phoneInput.classList.add('error');
                    isValid = false;
                }
                
                placeOrderBtn.disabled = !isValid;
                return isValid;
            }
            
            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            function isValidPhone(phone) {
                const re = /^[0-9\s\-\+\(\)]{10,}$/;
                return re.test(phone.replace(/\s/g, ''));
            }
            
            // Real-time validation
            formInputs.forEach(input => {
                input.addEventListener('input', validateForm);
                input.addEventListener('blur', validateForm);
            });
            
            // Initialize validation
            validateForm();
            
            // Place order button click
            placeOrderBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (!validateForm()) {
                    alert('Please fill in all required fields correctly.');
                    return;
                }
                
                // Show loading state
                const originalText = placeOrderBtn.innerHTML;
                placeOrderBtn.innerHTML = `
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        Processing Order...
                    </div>
                `;
                placeOrderBtn.disabled = true;
                
                // Simulate API call
                setTimeout(() => {
                    // Get form data
                    const orderData = {
                        customerName: document.getElementById('customerName').value,
                        customerPhone: document.getElementById('customerPhone').value,
                        customerEmail: document.getElementById('customerEmail').value,
                        deliveryAddress: document.getElementById('deliveryAddress').value,
                        paymentMethod: document.querySelector('.payment-method.active').dataset.method,
                        totalAmount: <?php echo $grand_total; ?>,
                        itemsCount: <?php echo $items_count; ?>,
                        orderNumber: 'ORD-' + Date.now().toString().slice(-8)
                    };
                    
                    // In a real app, you would send this data to the server
                    // For now, we'll just show success modal
                    
                    // Update success modal text
                    document.getElementById('orderConfirmationText').innerHTML = `
                        Thank you for your order! Your order <strong>#${orderData.orderNumber}</strong> has been received.
                        <br><br>
                        We'll deliver to:<br>
                        <strong>${orderData.deliveryAddress}</strong>
                        <br><br>
                        Payment method: ${orderData.paymentMethod.toUpperCase()}
                    `;
                    
                    // Show success modal
                    document.getElementById('successModal').classList.add('active');
                    
                    // Reset button
                    placeOrderBtn.innerHTML = originalText;
                    placeOrderBtn.disabled = false;
                    
                    // Clear cart (in a real app, this would be done after successful payment)
                    // For demo purposes, we'll clear it after order
                    // Note: In production, only clear after successful payment confirmation
                    
                }, 2000);
            });
            
            // Checkout steps animation
            const steps = document.querySelectorAll('.step');
            let currentStep = 0;
            
            function updateSteps() {
                steps.forEach((step, index) => {
                    step.classList.remove('active', 'completed');
                    if (index < currentStep) {
                        step.classList.add('completed');
                    } else if (index === currentStep) {
                        step.classList.add('active');
                    }
                });
            }
            
            // Simulate step progression (for demo)
            setTimeout(() => {
                currentStep = 1;
                updateSteps();
            }, 1000);
            
            setTimeout(() => {
                currentStep = 2;
                updateSteps();
            }, 2000);
            
            setTimeout(() => {
                currentStep = 3;
                updateSteps();
            }, 3000);
        });
        
        function closeModal() {
            document.getElementById('successModal').classList.remove('active');
            
            // Redirect to orders page after a delay
            setTimeout(() => {
                window.location.href = 'order.php';
            }, 500);
        }
        
        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
        
        // Auto-fill form for demo
        window.addEventListener('load', function() {
            const nameInput = document.getElementById('customerName');
            const phoneInput = document.getElementById('customerPhone');
            const emailInput = document.getElementById('customerEmail');
            const addressInput = document.getElementById('deliveryAddress');
            
            if (!nameInput.value) nameInput.value = 'John Doe';
            if (!phoneInput.value) phoneInput.value = '0712 345 678';
            if (!emailInput.value) emailInput.value = 'customer@example.com';
            if (!addressInput.value) addressInput.value = 'Kahawa Wendani, Nairobi, House No. 123';
            
            // Trigger validation
            const event = new Event('input');
            nameInput.dispatchEvent(event);
            phoneInput.dispatchEvent(event);
            emailInput.dispatchEvent(event);
            addressInput.dispatchEvent(event);
        });
    </script>
</body>
</html>