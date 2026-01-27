<?php
include 'config.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php?redirect=order');
    exit();
}

// Calculate cart count for header
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}

// Sample order data for demonstration
$orders = [
    [
        'id' => 'ORD-' . rand(1000, 9999),
        'total_amount' => 2450.00,
        'status' => 'delivered',
        'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'delivery_address' => 'Kahawa Wendani, Nairobi, House No. 123',
        'items_count' => 5,
        'customer_name' => isset($_SESSION['username']) ? $_SESSION['username'] : 'Customer'
    ],
    [
        'id' => 'ORD-' . rand(1000, 9999),
        'total_amount' => 1800.50,
        'status' => 'processing',
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'delivery_address' => 'Kahawa Wendani, Nairobi, Near Main Market',
        'items_count' => 3,
        'customer_name' => isset($_SESSION['username']) ? $_SESSION['username'] : 'Customer'
    ],
    [
        'id' => 'ORD-' . rand(1000, 9999),
        'total_amount' => 3200.00,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s'),
        'delivery_address' => 'Kahawa Wendani, Nairobi, Phase 2',
        'items_count' => 7,
        'customer_name' => isset($_SESSION['username']) ? $_SESSION['username'] : 'Customer'
    ],
    [
        'id' => 'ORD-' . rand(1000, 9999),
        'total_amount' => 4500.75,
        'status' => 'shipped',
        'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        'delivery_address' => 'Kahawa Wendani, Nairobi, Opposite School',
        'items_count' => 8,
        'customer_name' => isset($_SESSION['username']) ? $_SESSION['username'] : 'Customer'
    ],
    [
        'id' => 'ORD-' . rand(1000, 9999),
        'total_amount' => 1250.00,
        'status' => 'cancelled',
        'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
        'delivery_address' => 'Kahawa Wendani, Nairobi, Flat 101',
        'items_count' => 4,
        'customer_name' => isset($_SESSION['username']) ? $_SESSION['username'] : 'Customer'
    ]
];

// Calculate statistics
$total_orders = count($orders);
$total_spent = array_sum(array_column($orders, 'total_amount'));
$active_orders = 0;
foreach ($orders as $order) {
    if (in_array($order['status'], ['pending', 'processing', 'shipped'])) {
        $active_orders++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Uchumi Grocery</title>
    
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

        /* Orders Page */
        .orders-page {
            padding: 60px 0;
            min-height: 60vh;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
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

        .orders-stats {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            box-shadow: var(--shadow-light);
            text-align: center;
            min-width: 150px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--light-text);
            font-size: 14px;
        }

        /* Orders Tabs */
        .orders-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .tab-btn {
            padding: 12px 30px;
            background: transparent;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            color: var(--light-text);
            cursor: pointer;
            transition: var(--transition);
        }

        .tab-btn.active,
        .tab-btn:hover {
            background: var(--primary-green);
            color: var(--white);
        }

        /* Orders List */
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .order-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .order-header {
            padding: 25px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .order-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .order-id {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-text);
        }

        .order-date {
            color: var(--light-text);
            font-size: 14px;
        }

        .order-status {
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background: #cce5ff;
            color: #004085;
        }

        .status-shipped {
            background: #d4edda;
            color: #155724;
        }

        .status-delivered {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .order-summary {
            padding: 25px;
            border-bottom: 1px solid #eee;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .summary-label {
            color: var(--light-text);
        }

        .summary-value {
            font-weight: 600;
        }

        .summary-total {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-green);
            border-top: 2px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
        }

        .order-items {
            padding: 25px;
        }

        .items-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark-text);
        }

        .items-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .item-row {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 10px;
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
        }

        .item-meta {
            display: flex;
            gap: 15px;
            color: var(--light-text);
            font-size: 14px;
        }

        .item-price {
            font-weight: 600;
            color: var(--primary-green);
            min-width: 100px;
            text-align: right;
        }

        .order-actions {
            padding: 25px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary-green);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--dark-green);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            color: var(--primary-green);
            border: 2px solid var(--primary-green);
        }

        .btn-secondary:hover {
            background: var(--primary-green);
            color: var(--white);
        }

        .btn-danger {
            background: var(--accent-red);
            color: var(--white);
        }

        .btn-danger:hover {
            background: #c62828;
        }

        /* Empty State */
        .empty-orders {
            text-align: center;
            padding: 80px 20px;
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow-light);
        }

        .empty-icon {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 30px;
        }

        .empty-title {
            font-size: 24px;
            margin-bottom: 15px;
            color: var(--dark-text);
        }

        .empty-text {
            color: var(--light-text);
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Order Timeline */
        .order-timeline {
            padding: 25px;
            border-top: 1px solid #eee;
        }

        .timeline-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark-text);
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 9px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e0e0e0;
        }

        .timeline-step {
            position: relative;
            margin-bottom: 25px;
        }

        .timeline-step:last-child {
            margin-bottom: 0;
        }

        .timeline-step::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--white);
            border: 3px solid #e0e0e0;
            z-index: 1;
        }

        .timeline-step.completed::before {
            border-color: var(--light-green);
            background: var(--light-green);
        }

        .timeline-step.active::before {
            border-color: var(--primary-green);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1);
        }

        .timeline-date {
            font-size: 12px;
            color: var(--light-text);
            margin-bottom: 5px;
        }

        .timeline-text {
            font-weight: 500;
            color: var(--dark-text);
        }

        /* Filter Section */
        .filter-section {
            background: var(--white);
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow-light);
            margin-bottom: 30px;
        }

        .filter-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark-text);
        }

        .filter-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            background: #f5f5f5;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            color: var(--dark-text);
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--primary-green);
            color: var(--white);
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
        @media (max-width: 992px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .orders-stats {
                width: 100%;
                justify-content: space-between;
            }
        }

        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .order-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }
            
            .btn {
                flex: 1;
                justify-content: center;
            }
            
            .item-row {
                flex-direction: column;
                text-align: center;
            }
            
            .item-price {
                text-align: center;
            }
            
            .orders-tabs {
                overflow-x: auto;
                padding-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 28px;
            }
            
            .stat-card {
                min-width: 120px;
            }
            
            .stat-number {
                font-size: 24px;
            }
            
            .filter-options {
                flex-direction: column;
            }
        }

        /* Loading Animation */
        .loading {
            text-align: center;
            padding: 60px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Print Styles */
        @media print {
            .announcement-bar,
            .main-header,
            .orders-tabs,
            .filter-section,
            .order-actions,
            .main-footer {
                display: none !important;
            }
            
            .order-card {
                box-shadow: none;
                border: 1px solid #ddd;
                margin-bottom: 20px;
            }
        }

        /* Animation for stats */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stat-card {
            animation: countUp 0.6s ease forwards;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stat-card:nth-child(3) {
            animation-delay: 0.4s;
        }

        /* Search Box */
        .search-box {
            margin-bottom: 30px;
        }

        .search-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 30px;
            font-size: 16px;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
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
                        <span class="cart-count"><?php echo $cart_count; ?></span>
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

    <!-- Orders Page -->
    <section class="orders-page">
        <div class="container">
            <div class="page-header animate__animated animate__fadeIn">
                <h1 class="page-title">My Orders</h1>
                <div class="orders-stats">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_orders; ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">KSh <?php echo number_format($total_spent, 2); ?></div>
                        <div class="stat-label">Total Spent</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $active_orders; ?></div>
                        <div class="stat-label">Active Orders</div>
                    </div>
                </div>
            </div>

            <!-- Search Box -->
            <div class="search-box animate__animated animate__fadeIn">
                <input type="text" class="search-input" id="searchOrders" placeholder="Search by order number or status...">
            </div>

            <!-- Filter Section -->
            <div class="filter-section animate__animated animate__fadeIn">
                <h3 class="filter-title">Filter Orders</h3>
                <div class="filter-options">
                    <button class="filter-btn active" data-filter="all">All Orders</button>
                    <button class="filter-btn" data-filter="pending">Pending</button>
                    <button class="filter-btn" data-filter="processing">Processing</button>
                    <button class="filter-btn" data-filter="shipped">Shipped</button>
                    <button class="filter-btn" data-filter="delivered">Delivered</button>
                    <button class="filter-btn" data-filter="cancelled">Cancelled</button>
                </div>
            </div>

            <!-- Orders Tabs -->
            <div class="orders-tabs animate__animated animate__fadeIn">
                <button class="tab-btn active" data-tab="all">All Orders</button>
                <button class="tab-btn" data-tab="recent">Last 30 Days</button>
                <button class="tab-btn" data-tab="year">This Year</button>
                <button class="tab-btn" data-tab="older">Older</button>
            </div>

            <?php if (empty($orders)): ?>
                <!-- Empty State -->
                <div class="empty-orders animate__animated animate__fadeIn">
                    <div class="empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3 class="empty-title">No Orders Yet</h3>
                    <p class="empty-text">You haven't placed any orders yet. Start shopping to see your order history here.</p>
                    <a href="product.php" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <!-- Orders List -->
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <?php
                        // Format order data
                        $order_id = $order['id'];
                        $total_amount = $order['total_amount'];
                        $status = $order['status'];
                        $created_at = $order['created_at'];
                        $delivery_address = $order['delivery_address'];
                        $items_count = $order['items_count'];
                        $customer_name = $order['customer_name'];
                        
                        // Determine status class and icon
                        $status_class = 'status-' . $status;
                        $status_icon = '';
                        
                        switch ($status) {
                            case 'pending':
                                $status_icon = 'fas fa-clock';
                                break;
                            case 'processing':
                                $status_icon = 'fas fa-cog';
                                break;
                            case 'shipped':
                                $status_icon = 'fas fa-shipping-fast';
                                break;
                            case 'delivered':
                                $status_icon = 'fas fa-check-circle';
                                break;
                            case 'cancelled':
                                $status_icon = 'fas fa-times-circle';
                                break;
                        }
                        
                        // Format dates
                        $order_date = date('F d, Y', strtotime($created_at));
                        $delivery_date = date('F d, Y', strtotime($created_at . ' +' . rand(2, 5) . ' days'));
                        ?>
                        
                        <div class="order-card animate__animated animate__fadeIn" data-status="<?php echo $status; ?>" data-date="<?php echo $created_at; ?>" data-order-id="<?php echo $order_id; ?>">
                            <!-- Order Header -->
                            <div class="order-header">
                                <div class="order-info">
                                    <div class="order-id">Order #<?php echo $order_id; ?></div>
                                    <div class="order-date">Placed on <?php echo $order_date; ?></div>
                                    <div class="order-customer">Customer: <?php echo $customer_name; ?></div>
                                </div>
                                <div class="order-status <?php echo $status_class; ?>">
                                    <i class="<?php echo $status_icon; ?>"></i>
                                    <?php echo ucfirst($status); ?>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="order-summary">
                                <div class="summary-row">
                                    <span class="summary-label">Items Total</span>
                                    <span class="summary-value">KSh <?php echo number_format($total_amount * 0.85, 2); ?></span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Delivery Fee</span>
                                    <span class="summary-value">
                                        <?php echo $total_amount >= 1000 ? 'FREE' : 'KSh 200'; ?>
                                    </span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Tax (16%)</span>
                                    <span class="summary-value">KSh <?php echo number_format($total_amount * 0.16, 2); ?></span>
                                </div>
                                <div class="summary-row summary-total">
                                    <span>Total Amount</span>
                                    <span>KSh <?php echo number_format($total_amount, 2); ?></span>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="order-items">
                                <h4 class="items-title">Order Items (<?php echo $items_count; ?>)</h4>
                                <div class="items-list">
                                    <?php 
                                    // Sample items for demonstration
                                    $sample_items = [
                                        ['name' => 'Fresh Tomatoes', 'price' => 150, 'quantity' => 2, 'icon' => 'carrot'],
                                        ['name' => 'Green Peppers', 'price' => 100, 'quantity' => 1, 'icon' => 'pepper-hot'],
                                        ['name' => 'Kale (Sukuma Wiki)', 'price' => 50, 'quantity' => 3, 'icon' => 'leaf'],
                                        ['name' => 'Avocado', 'price' => 80, 'quantity' => 2, 'icon' => 'seedling'],
                                        ['name' => 'Oranges', 'price' => 120, 'quantity' => 1, 'icon' => 'apple-alt'],
                                        ['name' => 'Milk 1L', 'price' => 120, 'quantity' => 2, 'icon' => 'wine-bottle'],
                                        ['name' => 'Bread Loaf', 'price' => 65, 'quantity' => 1, 'icon' => 'bread-slice'],
                                        ['name' => 'Eggs (Tray)', 'price' => 350, 'quantity' => 1, 'icon' => 'egg']
                                    ];
                                    
                                    // Show 3-5 sample items
                                    $show_items = min($items_count, 5);
                                    for ($i = 0; $i < $show_items; $i++):
                                        $item = $sample_items[$i % count($sample_items)];
                                        $item_total = $item['price'] * $item['quantity'];
                                    ?>
                                    <div class="item-row">
                                        <div class="item-image">
                                            <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                                        </div>
                                        <div class="item-details">
                                            <div class="item-name"><?php echo $item['name']; ?></div>
                                            <div class="item-meta">
                                                <span>Qty: <?php echo $item['quantity']; ?></span>
                                                <span>Price: KSh <?php echo number_format($item['price'], 2); ?></span>
                                            </div>
                                        </div>
                                        <div class="item-price">
                                            KSh <?php echo number_format($item_total, 2); ?>
                                        </div>
                                    </div>
                                    <?php endfor; ?>
                                    
                                    <?php if ($items_count > 5): ?>
                                    <div class="item-row" style="justify-content: center; background: transparent;">
                                        <span style="color: var(--light-text);">
                                            + <?php echo ($items_count - 5); ?> more items
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Order Timeline -->
                            <div class="order-timeline">
                                <h4 class="timeline-title">Order Timeline</h4>
                                <div class="timeline">
                                    <div class="timeline-step <?php echo in_array($status, ['processing', 'shipped', 'delivered']) ? 'completed' : ''; ?> <?php echo $status == 'pending' ? 'active' : ''; ?>">
                                        <div class="timeline-date"><?php echo $order_date; ?></div>
                                        <div class="timeline-text">Order Placed</div>
                                    </div>
                                    <div class="timeline-step <?php echo in_array($status, ['shipped', 'delivered']) ? 'completed' : ''; ?> <?php echo $status == 'processing' ? 'active' : ''; ?>">
                                        <div class="timeline-date">
                                            <?php echo $status == 'pending' ? 'Expected' : date('F d', strtotime($created_at . ' +1 day')); ?>
                                        </div>
                                        <div class="timeline-text">Order Confirmed</div>
                                    </div>
                                    <div class="timeline-step <?php echo $status == 'delivered' ? 'completed' : ''; ?> <?php echo $status == 'shipped' ? 'active' : ''; ?>">
                                        <div class="timeline-date">
                                            <?php 
                                            if ($status == 'pending' || $status == 'processing') {
                                                echo 'Expected';
                                            } elseif ($status == 'shipped') {
                                                echo 'Shipped';
                                            } else {
                                                echo date('F d', strtotime($created_at . ' +2 days'));
                                            }
                                            ?>
                                        </div>
                                        <div class="timeline-text">Order Shipped</div>
                                    </div>
                                    <div class="timeline-step <?php echo $status == 'delivered' ? 'completed active' : ''; ?>">
                                        <div class="timeline-date">
                                            <?php 
                                            if ($status == 'delivered') {
                                                echo $delivery_date;
                                            } else {
                                                echo 'Expected ' . $delivery_date;
                                            }
                                            ?>
                                        </div>
                                        <div class="timeline-text">Order Delivered</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Actions -->
                            <div class="order-actions">
                                <div class="delivery-info">
                                    <strong>Delivery Address:</strong>
                                    <p><?php echo htmlspecialchars($delivery_address); ?></p>
                                </div>
                                <div class="action-buttons">
                                    <button class="btn btn-secondary" onclick="printOrder('<?php echo $order_id; ?>')">
                                        <i class="fas fa-print"></i> Print Invoice
                                    </button>
                                    <?php if ($status == 'pending' || $status == 'processing'): ?>
                                        <button class="btn btn-danger" onclick="cancelOrder('<?php echo $order_id; ?>')">
                                            <i class="fas fa-times"></i> Cancel Order
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($status == 'delivered'): ?>
                                        <button class="btn btn-primary" onclick="reorder('<?php echo $order_id; ?>')">
                                            <i class="fas fa-redo"></i> Reorder
                                        </button>
                                        <button class="btn btn-secondary" onclick="rateOrder('<?php echo $order_id; ?>')">
                                            <i class="fas fa-star"></i> Rate Order
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
                        <li><a href="order.php" style="color: #999; text-decoration: none; transition: var(--transition); display: block; padding: 5px 0;">
                            <i class="fas fa-chevron-right" style="margin-right: 10px;"></i> Orders
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabBtns = document.querySelectorAll('.tab-btn');
            const orderCards = document.querySelectorAll('.order-card');
            
            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Remove active class from all tabs
                    tabBtns.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked tab
                    btn.classList.add('active');
                    
                    const tab = btn.dataset.tab;
                    
                    // Filter orders based on tab
                    orderCards.forEach(card => {
                        const orderDate = new Date(card.dataset.date);
                        const now = new Date();
                        const thirtyDaysAgo = new Date(now.getTime() - (30 * 24 * 60 * 60 * 1000));
                        const startOfYear = new Date(now.getFullYear(), 0, 1);
                        
                        let showCard = true;
                        
                        switch(tab) {
                            case 'recent':
                                showCard = orderDate >= thirtyDaysAgo;
                                break;
                            case 'year':
                                showCard = orderDate >= startOfYear;
                                break;
                            case 'older':
                                showCard = orderDate < startOfYear;
                                break;
                            // 'all' shows all cards
                        }
                        
                        if (showCard) {
                            card.style.display = 'block';
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, 100);
                        } else {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';
                            setTimeout(() => {
                                card.style.display = 'none';
                            }, 300);
                        }
                    });
                });
            });
            
            // Filter functionality
            const filterBtns = document.querySelectorAll('.filter-btn');
            
            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Remove active class from all filter buttons
                    filterBtns.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked button
                    btn.classList.add('active');
                    
                    const filter = btn.dataset.filter;
                    
                    // Filter orders based on status
                    orderCards.forEach(card => {
                        if (filter === 'all' || card.dataset.status === filter) {
                            card.style.display = 'block';
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, 100);
                        } else {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';
                            setTimeout(() => {
                                card.style.display = 'none';
                            }, 300);
                        }
                    });
                });
            });
            
            // Search functionality
            const searchInput = document.getElementById('searchOrders');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                orderCards.forEach(card => {
                    const orderId = card.dataset.orderId.toLowerCase();
                    const status = card.dataset.status;
                    
                    if (orderId.includes(searchTerm) || status.includes(searchTerm)) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 100);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                });
            });
            
            // Animate processing icons
            const processingIcons = document.querySelectorAll('.fa-cog');
            processingIcons.forEach(icon => {
                icon.classList.add('fa-spin');
            });
        });
        
        // Order actions functions
        function printOrder(orderId) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Order Invoice #${orderId}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .invoice-header { text-align: center; margin-bottom: 30px; }
                        .invoice-header h1 { color: #2e7d32; }
                        .invoice-details { margin-bottom: 20px; }
                        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                        .total-row { font-weight: bold; }
                        .thank-you { text-align: center; margin-top: 30px; font-style: italic; color: #2e7d32; }
                        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class="invoice-header">
                        <h1>Uchumi Grocery</h1>
                        <p>Kahawa Wendani, Nairobi</p>
                        <p>Phone: 0712 345 678</p>
                        <h2>Invoice for Order #${orderId}</h2>
                        <p>Date: ${new Date().toLocaleDateString()}</p>
                    </div>
                    <div class="invoice-details">
                        <p>Thank you for your order! This is your invoice.</p>
                    </div>
                    <table class="items-table">
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                        <tr>
                            <td>Sample Item 1</td>
                            <td>2</td>
                            <td>KSh 150.00</td>
                            <td>KSh 300.00</td>
                        </tr>
                        <tr>
                            <td>Sample Item 2</td>
                            <td>1</td>
                            <td>KSh 100.00</td>
                            <td>KSh 100.00</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3">Total Amount</td>
                            <td>KSh 400.00</td>
                        </tr>
                    </table>
                    <div class="thank-you">
                        <p>Thank you for shopping with Uchumi Grocery!</p>
                    </div>
                    <div class="footer">
                        <p>Uchumi Grocery &copy; ${new Date().getFullYear()} | Fresh Kenyan Produce</p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }
        
        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel order #' + orderId + '?')) {
                // Find the order card
                const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);
                const originalHTML = orderCard.innerHTML;
                
                // Show loading state
                orderCard.innerHTML = `
                    <div class="loading">
                        <div class="spinner"></div>
                        <p>Cancelling order...</p>
                    </div>
                `;
                
                // Simulate API call
                setTimeout(() => {
                    // Show success message
                    alert('Order #' + orderId + ' has been cancelled successfully!');
                    
                    // Update the order status in the UI
                    orderCard.innerHTML = originalHTML.replace('pending', 'cancelled').replace('processing', 'cancelled');
                    
                    // Reload to update statistics
                    setTimeout(() => location.reload(), 1000);
                }, 1500);
            }
        }
        
        function reorder(orderId) {
            if (confirm('Add all items from order #' + orderId + ' to cart?')) {
                // Show loading
                const reorderBtn = document.querySelector(`[onclick*="${orderId}"]`);
                const originalText = reorderBtn.innerHTML;
                
                reorderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding to cart...';
                reorderBtn.disabled = true;
                
                // Simulate API call
                setTimeout(() => {
                    // Update cart count
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        const currentCount = parseInt(cartCount.textContent) || 0;
                        cartCount.textContent = currentCount + 3; // Add 3 items
                        
                        // Add pulse animation
                        cartCount.style.animation = 'none';
                        cartCount.offsetHeight; // Trigger reflow
                        cartCount.style.animation = 'pulse 0.5s ease-in-out';
                        
                        // Remove animation after it completes
                        setTimeout(() => {
                            cartCount.style.animation = '';
                        }, 500);
                    }
                    
                    // Show success message
                    alert('Items from order #' + orderId + ' have been added to your cart!');
                    
                    // Restore button
                    reorderBtn.innerHTML = originalText;
                    reorderBtn.disabled = false;
                }, 2000);
            }
        }
        
        function rateOrder(orderId) {
            const rating = prompt('Rate your order #' + orderId + ' from 1 to 5 stars:');
            if (rating !== null) {
                const numRating = parseInt(rating);
                if (numRating >= 1 && numRating <= 5) {
                    alert('Thank you for your ' + rating + '-star rating for order #' + orderId + '!');
                } else {
                    alert('Please enter a valid rating between 1 and 5.');
                }
            }
        }
        
        // Add CSS for pulse animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.2); }
                100% { transform: scale(1); }
            }
            
            .fa-spin {
                animation: fa-spin 2s infinite linear;
            }
            
            @keyframes fa-spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>