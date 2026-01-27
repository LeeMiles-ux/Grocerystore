<?php 
include 'config.php';
// Get featured products for display
$featured_products = [];
$sql = "SELECT * FROM products LIMIT 8";  // <-- CHANGED THIS LINE
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $featured_products[] = $row;
    }
}

// Calculate cart count
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uchumi Grocery | Fresh Kenyan Produce Delivered - Kahawa Wendani</title>
    <meta name="description" content="Fresh vegetables, fruits, and groceries delivered to your doorstep in Kahawa Wendani. Best quality Kenyan produce at affordable prices.">
    
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

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark-text);
            background: var(--white);
            overflow-x: hidden;
        }

        /* Announcement Bar */
        .announcement-bar {
            background: linear-gradient(90deg, var(--primary-green), var(--light-green));
            color: var(--white);
            padding: 12px 0;
            font-size: 14px;
            position: relative;
            overflow: hidden;
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
            animation: slideInLeft 0.8s ease;
        }

        .announcement-text i {
            color: var(--accent-orange);
            font-size: 16px;
        }

        .offer-tag {
            background: var(--accent-red);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Header */
        .main-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: var(--white);
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .header-scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
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
            position: relative;
            overflow: hidden;
        }

        .logo::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="%23ffffff" opacity="0.2" d="M50,0 C77.614237,0 100,22.385763 100,50 C100,77.614237 77.614237,100 50,100 C22.385763,100 0,77.614237 0,50 C0,22.385763 22.385763,0 50,0 Z M50,10 C27.90861,10 10,27.90861 10,50 C10,72.09139 27.90861,90 50,90 C72.09139,90 90,72.09139 90,50 C90,27.90861 72.09139,10 50,10 Z"/></svg>');
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

        .logo-tagline {
            color: var(--accent-orange);
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 1px;
        }

        /* Search Bar */
        .search-container {
            flex: 1;
            max-width: 600px;
            margin: 0 30px;
        }

        .search-box {
            position: relative;
            width: 100%;
        }

        .search-box input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 30px;
            font-size: 16px;
            transition: var(--transition);
            background: #f8f9fa;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--light-green);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: var(--white);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-btn:hover {
            transform: translateY(-50%) scale(1.1);
        }

        /* Header Actions */
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
            transform: translateY(-2px);
        }

        .action-icon {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .action-text {
            font-size: 12px;
            font-weight: 500;
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
            transition: var(--transition);
        }

        .cart-count.pulse {
            animation: pulse 0.5s ease-in-out;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 25px;
            background: #f5f5f5;
            transition: var(--transition);
        }

        .user-profile:hover {
            background: #e8f5e9;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            height: 90vh;
            min-height: 600px;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero-content {
            max-width: 800px;
            color: var(--white);
            position: relative;
            z-index: 2;
        }

        .hero-subtitle {
            font-size: 18px;
            color: var(--accent-orange);
            margin-bottom: 15px;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .hero-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 64px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            background: linear-gradient(to right, #ffffff, #ffcc80);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeInUp 1s ease;
        }

        .hero-description {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 16px 40px;
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

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-orange), #ffb74d);
            color: var(--white);
            box-shadow: 0 10px 20px rgba(255, 152, 0, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255, 152, 0, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: var(--white);
            border: 2px solid var(--white);
        }

        .btn-secondary:hover {
            background: var(--white);
            color: var(--dark-text);
        }

        .hero-stats {
            display: flex;
            gap: 40px;
            margin-top: 60px;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 40px;
            font-weight: 700;
            color: var(--accent-orange);
            display: block;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: var(--light-bg);
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 42px;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-orange);
            border-radius: 2px;
        }

        .section-title p {
            color: var(--light-text);
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: var(--white);
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-green), var(--light-green));
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 32px;
            color: var(--primary-green);
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon {
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: var(--white);
        }

        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--dark-text);
        }

        .feature-card p {
            color: var(--light-text);
            font-size: 16px;
        }

        /* Products Section */
        .products-section {
            padding: 100px 0;
            background: var(--white);
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .products-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 25px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            background: transparent;
            color: var(--dark-text);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--primary-green);
            color: var(--white);
            border-color: var(--primary-green);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow);
        }

        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--accent-red);
            color: var(--white);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 1;
        }

        .product-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.1);
        }

        .product-overlay {
            position: absolute;
            bottom: -50px;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            transition: var(--transition);
            display: flex;
            gap: 10px;
        }

        .product-card:hover .product-overlay {
            bottom: 0;
        }

        .quick-view-btn {
            flex: 1;
            padding: 10px;
            background: var(--primary-green);
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: var(--transition);
        }

        .quick-view-btn:hover {
            background: var(--dark-green);
        }

        .product-info {
            padding: 25px;
        }

        .product-category {
            color: var(--light-green);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .product-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark-text);
        }

        .product-description {
            color: var(--light-text);
            font-size: 14px;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .current-price {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-green);
        }

        .original-price {
            font-size: 16px;
            color: var(--light-text);
            text-decoration: line-through;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: var(--light-text);
        }

        .rating {
            color: var(--accent-orange);
        }

        .stock {
            color: var(--light-green);
            font-weight: 500;
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .add-to-cart-btn:hover {
            background: linear-gradient(135deg, var(--dark-green), var(--primary-green));
            transform: translateY(-2px);
        }

        .add-to-cart-btn.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .login-required {
            background: #f5f5f5;
            color: var(--dark-text);
        }

        .login-required:hover {
            background: #e0e0e0;
        }

        /* Categories Section */
        .categories-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .category-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            position: relative;
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow);
        }

        .category-image {
            height: 200px;
            overflow: hidden;
        }

        .category-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .category-card:hover .category-image img {
            transform: scale(1.1);
        }

        .category-content {
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .category-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -55px auto 20px;
            font-size: 28px;
            color: var(--white);
            box-shadow: 0 10px 20px rgba(46, 125, 50, 0.2);
        }

        .category-card h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: var(--dark-text);
        }

        .category-card p {
            color: var(--light-text);
            margin-bottom: 20px;
        }

        .category-link {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
        }

        .category-link:hover {
            color: var(--dark-green);
            gap: 12px;
        }

        /* Testimonials */
        .testimonials-section {
            padding: 100px 0;
            background: var(--white);
        }

        .testimonials-slider {
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

        .testimonial-card {
            background: var(--light-bg);
            padding: 40px;
            border-radius: 15px;
            box-shadow: var(--shadow-light);
            margin: 20px;
            transition: var(--transition);
        }

        .testimonial-content {
            font-size: 18px;
            font-style: italic;
            margin-bottom: 25px;
            color: var(--dark-text);
            position: relative;
            padding-left: 30px;
        }

        .testimonial-content::before {
            content: '"';
            position: absolute;
            left: 0;
            top: -10px;
            font-size: 60px;
            color: var(--light-green);
            opacity: 0.3;
            font-family: serif;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
        }

        .author-info h4 {
            font-size: 18px;
            margin-bottom: 5px;
            color: var(--dark-text);
        }

        .author-info p {
            color: var(--light-text);
            font-size: 14px;
        }

        /* Newsletter */
        .newsletter-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: var(--white);
        }

        .newsletter-content {
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .newsletter-content h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .newsletter-content p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .newsletter-form {
            display: flex;
            gap: 10px;
            max-width: 500px;
            margin: 0 auto;
        }

        .newsletter-form input {
            flex: 1;
            padding: 15px 25px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            outline: none;
        }

        .newsletter-form button {
            padding: 15px 40px;
            background: var(--accent-orange);
            color: var(--white);
            border: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .newsletter-form button:hover {
            background: #ff8a00;
            transform: translateY(-2px);
        }

        /* Footer */
        .main-footer {
            background: #1a1a1a;
            color: var(--white);
            padding: 80px 0 30px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 50px;
        }

        .footer-col h3 {
            font-size: 20px;
            margin-bottom: 25px;
            color: var(--white);
            position: relative;
            padding-bottom: 10px;
        }

        .footer-col h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--accent-orange);
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .footer-logo i {
            font-size: 32px;
            color: var(--light-green);
        }

        .footer-logo h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--white);
        }

        .footer-description {
            color: #999;
            margin-bottom: 25px;
            line-height: 1.8;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            text-decoration: none;
            transition: var(--transition);
        }

        .social-links a:hover {
            background: var(--primary-green);
            transform: translateY(-3px);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 15px;
        }

        .footer-links a {
            color: #999;
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-links a:hover {
            color: var(--white);
            padding-left: 5px;
        }

        .contact-info {
            list-style: none;
        }

        .contact-info li {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }

        .contact-info i {
            color: var(--accent-orange);
            font-size: 18px;
            margin-top: 3px;
        }

        .contact-info span {
            color: #999;
            line-height: 1.6;
        }

        .footer-bottom {
            padding-top: 30px;
            border-top: 1px solid #333;
            text-align: center;
            color: #999;
        }

        .payment-methods {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            font-size: 30px;
        }

        .payment-methods i {
            color: #666;
            transition: var(--transition);
        }

        .payment-methods i:hover {
            color: var(--white);
        }

        /* Cart Notification */
        .cart-notification {
            position: fixed;
            top: 100px;
            right: 20px;
            background: var(--primary-green);
            color: var(--white);
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 10000;
            animation: slideInRight 0.3s ease;
        }

        .cart-notification.error {
            background: var(--accent-red);
        }

        .cart-notification i {
            font-size: 20px;
        }

        /* Animations */
        @keyframes slideInLeft {
            from {
                transform: translateX(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .hero-title {
                font-size: 48px;
            }
        }

        @media (max-width: 992px) {
            .header-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .search-container {
                margin: 0;
                width: 100%;
                max-width: 100%;
            }
            
            .hero-title {
                font-size: 36px;
            }
            
            .hero-stats {
                justify-content: center;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                height: auto;
                padding: 100px 0;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .features-grid,
            .products-grid,
            .categories-grid,
            .footer-grid {
                grid-template-columns: 1fr;
            }
            
            .products-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .products-filter {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 28px;
            }
            
            .section-title h2 {
                font-size: 32px;
            }
            
            .testimonial-content {
                padding-left: 20px;
            }
            
            .testimonial-content::before {
                font-size: 40px;
            }
        }

        /* Scroll to Top Button */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary-green);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 999;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .scroll-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top:hover {
            background: var(--dark-green);
            transform: translateY(-5px);
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
    <header class="main-header" id="mainHeader">
        <div class="container">
            <div class="header-container">
                <!-- Logo -->
                <div class="logo-section">
                    <div class="logo">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <div class="logo-text">
                        <h1>Uchumi Grocery</h1>
                        <p class="logo-tagline">FRESH KENYAN PRODUCE</p>
                    </div>
                </div>

                <!-- Search -->
                <div class="search-container">
                    <div class="search-box">
                        <input type="text" placeholder="Search for fresh vegetables, fruits, groceries..." id="searchInput">
                        <button class="search-btn" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="header-actions">
                    <a href="cart.php" class="action-item">
                        <i class="fas fa-shopping-cart action-icon"></i>
                        <span class="action-text">Cart</span>
                        <span class="cart-count" id="cartCount"><?php echo $cart_count; ?></span>
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                        <div class="user-profile" id="userProfile">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                            </div>
                            <div class="user-info">
                                <strong><?php echo $_SESSION['username']; ?></strong>
                                <br>
                                <small><?php echo $_SESSION['full_name'] ?? 'Member'; ?></small>
                            </div>
                        </div>
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

    <!-- Hero Section -->
    <section class="hero-section" id="hero">
        <div class="container">
            <div class="hero-content">
                <h5 class="hero-subtitle animate__animated animate__fadeIn">Fresh & Organic</h5>
                <h1 class="hero-title animate__animated animate__fadeInUp">Experience The Taste of <br>Fresh Kenyan Produce</h1>
                <p class="hero-description animate__animated animate__fadeInUp">
                    Direct from our farms to your table. We deliver the freshest vegetables, fruits, 
                    and groceries right to your doorstep in Kahawa Wendani.
                </p>
                <div class="hero-buttons animate__animated animate__fadeInUp">
                    <a href="product.php" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Shop Now
                    </a>
                    <a href="#categories" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Browse Categories
                    </a>
                </div>
                <div class="hero-stats animate__animated animate__fadeInUp">
                    <div class="stat-item">
                        <span class="stat-number" data-count="5000">0</span>
                        <span class="stat-label">Happy Customers</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" data-count="150">0</span>
                        <span class="stat-label">Fresh Products</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" data-count="500">0</span>
                        <span class="stat-label">Daily Orders</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" data-count="24">0</span>
                        <span class="stat-label">Hour Delivery</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose Uchumi Grocery?</h2>
                <p>We provide the best grocery shopping experience in Kahawa Wendani</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Fast Delivery</h3>
                    <p>Same day delivery within Kahawa Wendani. Order by 2 PM for evening delivery.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Farm Fresh</h3>
                    <p>Direct sourcing from local Kenyan farmers ensuring the freshest produce.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h3>Best Prices</h3>
                    <p>Affordable prices without compromising on quality. Price match guarantee.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Safe Packaging</h3>
                    <p>Hygienic and secure packaging for all your groceries.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="products-section">
        <div class="container">
            <div class="products-header">
                <div class="section-title">
                    <h2>Featured Products</h2>
                    <p>Fresh picks from our farm</p>
                </div>
                <div class="products-filter">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="vegetables">Vegetables</button>
                    <button class="filter-btn" data-filter="fruits">Fruits</button>
                    <button class="filter-btn" data-filter="groceries">Groceries</button>
                </div>
            </div>
            <div class="products-grid">
                <?php if (!empty($featured_products)): ?>
                    <?php foreach($featured_products as $product): ?>
                        <?php
                        $discount_price = isset($product['discount']) && $product['discount'] > 0 ? 
                            $product['price'] * (1 - $product['discount']/100) : 
                            $product['price'];
                        ?>
                        <div class="product-card" data-category="<?php echo strtolower($product['category'] ?? 'vegetables'); ?>">
                            <?php if(isset($product['discount']) && $product['discount'] > 0): ?>
                                <div class="product-badge">-<?php echo $product['discount']; ?>%</div>
                            <?php endif; ?>
                            
                            <div class="product-image">
                                <img src="images/products/<?php echo $product['image'] ?? 'placeholder.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     onerror="this.src='images/placeholder.jpg'">
                                <div class="product-overlay">
                                    <button class="quick-view-btn" data-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-eye"></i> Quick View
                                    </button>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <div class="product-category"><?php echo $product['category'] ?? 'Vegetables'; ?></div>
                                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-description"><?php echo htmlspecialchars($product['description'] ?? 'Fresh produce'); ?></p>
                                
                                <div class="product-price">
                                    <span class="current-price">KSh <?php echo number_format($discount_price, 2); ?></span>
                                    <?php if(isset($product['discount']) && $product['discount'] > 0): ?>
                                        <span class="original-price">KSh <?php echo number_format($product['price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-meta">
                                    <div class="rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                        <span>(4.5)</span>
                                    </div>
                                    <div class="stock">
                                        <i class="fas fa-check-circle"></i> In Stock
                                    </div>
                                </div>
                                
                                <?php if (isLoggedIn()): ?>
                                    <form method="post" class="add-to-cart-form" data-id="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="add-to-cart-btn">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" class="add-to-cart-btn login-required">
                                        <i class="fas fa-sign-in-alt"></i> Login to Purchase
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-products" style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--light-text);">
                        No products available. Please add products to the database.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="categories-section" id="categories">
        <div class="container">
            <div class="section-title">
                <h2>Shop by Category</h2>
                <p>Browse our wide range of fresh produce</p>
            </div>
            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1597362925123-77861d3fbac7?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Vegetables">
                    </div>
                    <div class="category-content">
                        <div class="category-icon">
                            <i class="fas fa-carrot"></i>
                        </div>
                        <h3>Vegetables</h3>
                        <p>Fresh leafy greens, root vegetables, and more</p>
                        <a href="product.php?category=vegetables" class="category-link">
                            Shop Now <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="category-card">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1610832958506-aa56368176cf?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Fruits">
                    </div>
                    <div class="category-content">
                        <div class="category-icon">
                            <i class="fas fa-apple-alt"></i>
                        </div>
                        <h3>Fruits</h3>
                        <p>Seasonal fruits, tropical delights, and berries</p>
                        <a href="product.php?category=fruits" class="category-link">
                            Shop Now <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="category-card">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Dairy">
                    </div>
                    <div class="category-content">
                        <div class="category-icon">
                            <i class="fas fa-cheese"></i>
                        </div>
                        <h3>Dairy & Eggs</h3>
                        <p>Fresh milk, eggs, cheese, and yogurt</p>
                        <a href="product.php?category=dairy" class="category-link">
                            Shop Now <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="category-card">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Groceries">
                    </div>
                    <div class="category-content">
                        <div class="category-icon">
                            <i class="fas fa-wine-bottle"></i>
                        </div>
                        <h3>Groceries</h3>
                        <p>Rice, flour, cooking oil, and essentials</p>
                        <a href="product.php?category=groceries" class="category-link">
                            Shop Now <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-title">
                <h2>What Our Customers Say</h2>
                <p>Join thousands of happy customers in Kahawa Wendani</p>
            </div>
            <div class="testimonials-slider">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        The quality of vegetables from Uchumi Grocery is amazing! 
                        Fresh, crisp, and delivered right to my doorstep. Highly recommended!
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">SM</div>
                        <div class="author-info">
                            <h4>Sarah Mwangi</h4>
                            <p>Kahawa Wendani Resident</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <h2>Stay Updated</h2>
                <p>Subscribe to our newsletter for the latest offers and fresh produce updates</p>
                <form class="newsletter-form" id="newsletterForm">
                    <input type="email" placeholder="Enter your email address" required>
                    <button type="submit">Subscribe</button>
                </form>
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
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="product.php"><i class="fas fa-chevron-right"></i> Products</a></li>
                        <li><a href="about.php"><i class="fas fa-chevron-right"></i> About Us</a></li>
                        <li><a href="contact.php"><i class="fas fa-chevron-right"></i> Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Categories</h3>
                    <ul class="footer-links">
                        <li><a href="product.php?category=vegetables"><i class="fas fa-chevron-right"></i> Vegetables</a></li>
                        <li><a href="product.php?category=fruits"><i class="fas fa-chevron-right"></i> Fruits</a></li>
                        <li><a href="product.php?category=dairy"><i class="fas fa-chevron-right"></i> Dairy & Eggs</a></li>
                        <li><a href="product.php?category=groceries"><i class="fas fa-chevron-right"></i> Groceries</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Contact Info</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Kahawa Wendani, Nairobi, Kenya</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>0712 345 678<br>0733 456 789</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>info@uchumigrocery.co.ke<br>orders@uchumigrocery.co.ke</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Mon - Sun: 6:00 AM - 10:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Uchumi Grocery Store, Kahawa Wendani. All rights reserved.</p>
                <div class="payment-methods">
                    <i class="fab fa-cc-mpesa" title="M-Pesa"></i>
                    <i class="fab fa-cc-visa" title="Visa"></i>
                    <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                    <i class="fas fa-money-bill-wave" title="Cash"></i>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <div class="scroll-top" id="scrollTop">
        <i class="fas fa-chevron-up"></i>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Header scroll effect
            const header = document.getElementById('mainHeader');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 100) {
                    header.classList.add('header-scrolled');
                } else {
                    header.classList.remove('header-scrolled');
                }
            });

            // Search functionality
            const searchBtn = document.getElementById('searchBtn');
            const searchInput = document.getElementById('searchInput');
            
            searchBtn.addEventListener('click', () => {
                const query = searchInput.value.trim();
                if (query) {
                    window.location.href = `product.php?search=${encodeURIComponent(query)}`;
                }
            });
            
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    searchBtn.click();
                }
            });

            // Product filtering
            const filterBtns = document.querySelectorAll('.filter-btn');
            const productCards = document.querySelectorAll('.product-card');
            
            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Remove active class from all buttons
                    filterBtns.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked button
                    btn.classList.add('active');
                    
                    const filter = btn.dataset.filter;
                    
                    // Filter products
                    productCards.forEach(card => {
                        if (filter === 'all' || card.dataset.category === filter) {
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

            // Quick view functionality
            const quickViewBtns = document.querySelectorAll('.quick-view-btn');
            quickViewBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const productId = btn.dataset.id;
                    alert('Quick view for product ID: ' + productId + ' - Feature coming soon!');
                });
            });

            // Number counter animation
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const target = parseInt(stat.dataset.count);
                const increment = target / 100;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.textContent = target.toLocaleString();
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current).toLocaleString();
                    }
                }, 20);
            });

            // Scroll to top button
            const scrollTopBtn = document.getElementById('scrollTop');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    scrollTopBtn.classList.add('visible');
                } else {
                    scrollTopBtn.classList.remove('visible');
                }
            });
            
            scrollTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Newsletter form
            const newsletterForm = document.getElementById('newsletterForm');
            newsletterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const email = newsletterForm.querySelector('input').value;
                if (email) {
                    alert('Thank you for subscribing! You will receive our latest offers soon.');
                    newsletterForm.reset();
                }
            });

            // User profile dropdown
            const userProfile = document.getElementById('userProfile');
            if (userProfile) {
                let dropdown = null;
                
                userProfile.addEventListener('click', (e) => {
                    e.stopPropagation();
                    
                    if (dropdown) {
                        dropdown.remove();
                        dropdown = null;
                    } else {
                        dropdown = document.createElement('div');
                        dropdown.className = 'user-dropdown';
                        dropdown.innerHTML = `
                            <a href="order.php"><i class="fas fa-box"></i> My Orders</a>
                            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        `;
                        
                        dropdown.style.cssText = `
                            position: absolute;
                            top: 100%;
                            right: 0;
                            background: white;
                            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                            border-radius: 10px;
                            padding: 15px 0;
                            min-width: 200px;
                            z-index: 1000;
                        `;
                        
                        dropdown.querySelectorAll('a').forEach(link => {
                            link.style.cssText = `
                                display: block;
                                padding: 10px 20px;
                                color: #333;
                                text-decoration: none;
                                transition: all 0.3s;
                            `;
                            link.addEventListener('mouseenter', () => {
                                link.style.background = '#f5f5f5';
                            });
                            link.addEventListener('mouseleave', () => {
                                link.style.background = 'transparent';
                            });
                        });
                        
                        userProfile.style.position = 'relative';
                        userProfile.appendChild(dropdown);
                        
                        // Close dropdown when clicking outside
                        document.addEventListener('click', closeDropdown);
                    }
                });
                
                function closeDropdown(e) {
                    if (dropdown && !userProfile.contains(e.target)) {
                        dropdown.remove();
                        dropdown = null;
                        document.removeEventListener('click', closeDropdown);
                    }
                }
            }

            // Animate elements on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    }
                });
            }, observerOptions);

            // Observe elements
            document.querySelectorAll('.feature-card, .product-card, .category-card').forEach(el => {
                observer.observe(el);
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId !== '#') {
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });

        // Add to cart functionality
        document.addEventListener('DOMContentLoaded', function() {
            const addToCartForms = document.querySelectorAll('.add-to-cart-form');
            
            addToCartForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    <?php if (!isLoggedIn()): ?>
                        alert('Please login to add items to cart');
                        window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
                        return;
                    <?php endif; ?>
                    
                    const productId = this.querySelector('input[name="product_id"]').value;
                    const submitBtn = this.querySelector('.add-to-cart-btn');
                    const originalText = submitBtn.innerHTML;
                    
                    // Show loading state
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    
                    // Send AJAX request
                    fetch('add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_id=${productId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update cart count
                            const cartCount = document.getElementById('cartCount');
                            if (cartCount) {
                                cartCount.textContent = data.cart_count;
                                cartCount.classList.add('pulse');
                                setTimeout(() => {
                                    cartCount.classList.remove('pulse');
                                }, 500);
                            }
                            
                            // Show success notification
                            showCartNotification(data.message, 'success');
                        } else {
                            showCartNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showCartNotification('Error adding item to cart. Please try again.', 'error');
                    })
                    .finally(() => {
                        // Restore button state
                        submitBtn.innerHTML = originalText;
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                    });
                });
            });
            
            // Cart notification function
            function showCartNotification(message, type = 'success') {
                // Remove existing notification
                const existingNotification = document.querySelector('.cart-notification');
                if (existingNotification) {
                    existingNotification.remove();
                }
                
                // Create notification
                const notification = document.createElement('div');
                notification.className = `cart-notification ${type === 'error' ? 'error' : ''}`;
                notification.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${message}</span>
                `;
                
                document.body.appendChild(notification);
                
                // Auto remove after 3 seconds
                setTimeout(() => {
                    notification.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
        });
    </script>
</body>
</html>