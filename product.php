<?php 
include 'config.php';
// Get products from database with optional filtering
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Build query based on filters
$sql = "SELECT * FROM products";
$where_clauses = [];

if ($category_filter != 'all') {
    $where_clauses[] = "category = '" . $conn->real_escape_string($category_filter) . "'";
}

if (!empty($search_query)) {
    $where_clauses[] = "(name LIKE '%" . $conn->real_escape_string($search_query) . "%' 
                         OR description LIKE '%" . $conn->real_escape_string($search_query) . "%')";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql .= " ORDER BY name ASC";
$result = $conn->query($sql);

// Get all distinct categories for filter
$categories_result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''");
$categories = [];
while($cat = $categories_result->fetch_assoc()) {
    $categories[] = $cat['category'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Kenyan Produce - Shop Online | Uchumi Grocery</title>
    <meta name="description" content="Shop fresh Kenyan vegetables, fruits, and groceries. Free delivery in Kahawa Wendani. Best quality produce at affordable prices.">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        /* Reuse the same CSS variables from index.php */
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

        /* Header Styles (same as index.php) */
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

        .announcement-text i {
            color: var(--accent-orange);
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

        .main-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: var(--white);
            box-shadow: var(--shadow-light);
            transition: var(--transition);
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

        .logo-tagline {
            color: var(--accent-orange);
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 1px;
        }

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

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: var(--white);
            padding: 60px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.1;
        }

        .page-header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .page-header p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .breadcrumb {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }

        .breadcrumb a {
            color: var(--white);
            text-decoration: none;
            opacity: 0.8;
            transition: var(--transition);
        }

        .breadcrumb a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        .breadcrumb span {
            opacity: 0.6;
        }

        /* Main Content */
        .products-main {
            padding: 60px 0;
            background: var(--light-bg);
            min-height: 60vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Filter and Sort Section */
        .filter-sort-section {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .filter-controls {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-label {
            font-weight: 600;
            color: var(--dark-text);
            white-space: nowrap;
        }

        .category-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .category-btn {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            background: transparent;
            color: var(--dark-text);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            white-space: nowrap;
        }

        .category-btn.active,
        .category-btn:hover {
            background: var(--primary-green);
            color: var(--white);
            border-color: var(--primary-green);
        }

        .sort-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sort-select {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            background: var(--white);
            color: var(--dark-text);
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            min-width: 180px;
        }

        .sort-select:focus {
            outline: none;
            border-color: var(--light-green);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .view-toggle {
            display: flex;
            gap: 5px;
            margin-left: 15px;
        }

        .view-btn {
            width: 40px;
            height: 40px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: transparent;
            color: var(--dark-text);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .view-btn.active,
        .view-btn:hover {
            background: var(--primary-green);
            color: var(--white);
            border-color: var(--primary-green);
        }

        /* Products Grid */
        .products-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .products-container.list-view {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .product-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .products-container.list-view .product-card {
            flex-direction: row;
            height: 200px;
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
            flex-shrink: 0;
        }

        .products-container.list-view .product-image {
            width: 200px;
            height: 100%;
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
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .products-container.list-view .product-info {
            padding: 20px;
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
            line-height: 1.4;
        }

        .products-container.list-view .product-title {
            font-size: 20px;
        }

        .product-description {
            color: var(--light-text);
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.6;
            flex: 1;
        }

        .products-container.list-view .product-description {
            display: block;
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

        .product-unit {
            color: var(--light-text);
            font-size: 14px;
            margin-left: auto;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 15px;
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

        .login-required {
            background: #f5f5f5;
            color: var(--dark-text);
        }

        .login-required:hover {
            background: #e0e0e0;
        }

        .products-container.list-view .add-to-cart-btn {
            width: auto;
            min-width: 180px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 50px;
            flex-wrap: wrap;
        }

        .page-btn {
            width: 45px;
            height: 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: var(--white);
            color: var(--dark-text);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
        }

        .page-btn.active,
        .page-btn:hover {
            background: var(--primary-green);
            color: var(--white);
            border-color: var(--primary-green);
        }

        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-btn.disabled:hover {
            background: var(--white);
            color: var(--dark-text);
            border-color: #e0e0e0;
        }

        /* Results Info */
        .results-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            color: var(--light-text);
            font-size: 14px;
        }

        .results-count span {
            font-weight: 600;
            color: var(--primary-green);
        }

        .clear-filters {
            color: var(--accent-red);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .clear-filters:hover {
            text-decoration: underline;
        }

        /* No Products Message */
        .no-products {
            text-align: center;
            padding: 80px 20px;
            grid-column: 1/-1;
        }

        .no-products i {
            font-size: 60px;
            color: #e0e0e0;
            margin-bottom: 20px;
        }

        .no-products h3 {
            font-size: 24px;
            color: var(--dark-text);
            margin-bottom: 15px;
        }

        .no-products p {
            color: var(--light-text);
            margin-bottom: 30px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 25px;
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
            box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 152, 0, 0.4);
        }

        /* Footer Styles (same as index.php) */
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

        /* Responsive Design */
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
            
            .page-header h1 {
                font-size: 36px;
            }
            
            .filter-sort-section {
                flex-direction: column;
                align-items: stretch;
            }
            
            .sort-controls {
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .announcement-bar .container {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .products-container.list-view .product-card {
                flex-direction: column;
                height: auto;
            }
            
            .products-container.list-view .product-image {
                width: 100%;
                height: 200px;
            }
            
            .page-header h1 {
                font-size: 28px;
            }
            
            .filter-controls {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .category-filter {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .category-btn,
            .sort-select {
                font-size: 14px;
                padding: 8px 15px;
            }
            
            .product-card {
                margin-bottom: 20px;
            }
            
            .results-info {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }

        /* Quick View Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: var(--white);
            border-radius: 15px;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--accent-red);
            color: var(--white);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            font-size: 20px;
            cursor: pointer;
            z-index: 1;
            transition: var(--transition);
        }

        .close-modal:hover {
            transform: rotate(90deg);
        }

        .modal-product {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding: 40px;
        }

        @media (max-width: 768px) {
            .modal-product {
                grid-template-columns: 1fr;
                gap: 30px;
            }
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
                    <a href="index.php" style="text-decoration: none; display: flex; align-items: center; gap: 15px;">
                        <div class="logo">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                        <div class="logo-text">
                            <h1>Uchumi Grocery</h1>
                            <p class="logo-tagline">FRESH KENYAN PRODUCE</p>
                        </div>
                    </a>
                </div>

                <!-- Search -->
                <div class="search-container">
                    <div class="search-box">
                        <input type="text" placeholder="Search for fresh vegetables, fruits, groceries..." 
                               id="searchInput"
                               value="<?php echo htmlspecialchars($search_query); ?>">
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
                        <span class="cart-count"><?php echo isLoggedIn() ? array_sum($_SESSION['cart']) : '0'; ?></span>
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

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Our Fresh Produce</h1>
            <p>Direct from Kenyan farms to your table. Shop the finest vegetables, fruits, and groceries.</p>
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <span>/</span>
                <span>Products</span>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="products-main">
        <div class="container">
            <!-- Filter and Sort Controls -->
            <div class="filter-sort-section">
                <div class="filter-controls">
                    <div class="filter-group">
                        <span class="filter-label">Filter by:</span>
                        <div class="category-filter">
                            <button class="category-btn <?php echo $category_filter == 'all' ? 'active' : ''; ?>" 
                                    data-category="all">
                                All Products
                            </button>
                            <?php foreach($categories as $category): ?>
                                <button class="category-btn <?php echo $category_filter == $category ? 'active' : ''; ?>" 
                                        data-category="<?php echo htmlspecialchars($category); ?>">
                                    <?php echo htmlspecialchars(ucfirst($category)); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="sort-controls">
                    <select class="sort-select" id="sortSelect">
                        <option value="name_asc">Sort by: Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="newest">Newest First</option>
                    </select>
                    
                    <div class="view-toggle">
                        <button class="view-btn active" id="gridViewBtn">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button class="view-btn" id="listViewBtn">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Info -->
            <div class="results-info">
                <div class="results-count">
                    Showing <span><?php echo $result->num_rows; ?></span> product<?php echo $result->num_rows != 1 ? 's' : ''; ?>
                    <?php if (!empty($search_query)): ?>
                        for "<span><?php echo htmlspecialchars($search_query); ?></span>"
                    <?php endif; ?>
                </div>
                <?php if ($category_filter != 'all' || !empty($search_query)): ?>
                    <a href="product.php" class="clear-filters">
                        <i class="fas fa-times"></i> Clear all filters
                    </a>
                <?php endif; ?>
            </div>

            <!-- Products Grid -->
            <div class="products-container" id="productsContainer">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($product = $result->fetch_assoc()): ?>
                        <?php
                        $discount_price = isset($product['discount']) && $product['discount'] > 0 ? 
                            $product['price'] * (1 - $product['discount']/100) : 
                            $product['price'];
                        ?>
                        <div class="product-card" 
                             data-id="<?php echo $product['id']; ?>"
                             data-name="<?php echo htmlspecialchars($product['name']); ?>"
                             data-price="<?php echo $product['price']; ?>"
                             data-category="<?php echo strtolower($product['category'] ?? 'vegetables'); ?>"
                             data-date="<?php echo $product['created_at'] ?? ''; ?>">
                            
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
                                <p class="product-description"><?php echo htmlspecialchars($product['description'] ?? 'Fresh produce from Kenyan farms'); ?></p>
                                
                                <div class="product-price">
                                    <span class="current-price">KSh <?php echo number_format($discount_price, 2); ?></span>
                                    <?php if(isset($product['discount']) && $product['discount'] > 0): ?>
                                        <span class="original-price">KSh <?php echo number_format($product['price'], 2); ?></span>
                                    <?php endif; ?>
                                    <span class="product-unit"><?php echo $product['unit'] ?? 'per kg'; ?></span>
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
                                    <form method="post" action="add_to_cart.php" class="add-to-cart-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="add-to-cart-btn">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php?redirect=product" class="add-to-cart-btn login-required">
                                        <i class="fas fa-sign-in-alt"></i> Login to Purchase
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-products">
                        <i class="fas fa-shopping-basket"></i>
                        <h3>No Products Found</h3>
                        <p>
                            <?php if (!empty($search_query)): ?>
                                We couldn't find any products matching "<?php echo htmlspecialchars($search_query); ?>".
                            <?php else: ?>
                                No products are currently available in this category.
                            <?php endif; ?>
                        </p>
                        <a href="product.php" class="btn btn-primary">
                            <i class="fas fa-store"></i> View All Products
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($result->num_rows > 0): ?>
                <div class="pagination">
                    <button class="page-btn disabled">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <button class="page-btn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </main>

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
                        <li><a href="#"><i class="fas fa-chevron-right"></i> About Us</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Contact</a></li>
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

    <!-- Quick View Modal -->
    <div class="modal" id="quickViewModal">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <div class="modal-product" id="modalProductContent">
                <!-- Content loaded via JavaScript -->
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Category Filter
            const categoryBtns = document.querySelectorAll('.category-btn');
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const category = btn.dataset.category;
                    const url = new URL(window.location.href);
                    
                    if (category === 'all') {
                        url.searchParams.delete('category');
                    } else {
                        url.searchParams.set('category', category);
                    }
                    
                    // Remove search query if it exists
                    url.searchParams.delete('search');
                    
                    window.location.href = url.toString();
                });
            });

            // Search Functionality
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            
            searchBtn.addEventListener('click', () => {
                performSearch();
            });
            
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
            
            function performSearch() {
                const query = searchInput.value.trim();
                const url = new URL(window.location.href);
                
                if (query) {
                    url.searchParams.set('search', query);
                } else {
                    url.searchParams.delete('search');
                }
                
                url.searchParams.delete('category');
                window.location.href = url.toString();
            }

            // View Toggle
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');
            const productsContainer = document.getElementById('productsContainer');
            
            gridViewBtn.addEventListener('click', () => {
                productsContainer.classList.remove('list-view');
                gridViewBtn.classList.add('active');
                listViewBtn.classList.remove('active');
                localStorage.setItem('productView', 'grid');
            });
            
            listViewBtn.addEventListener('click', () => {
                productsContainer.classList.add('list-view');
                listViewBtn.classList.add('active');
                gridViewBtn.classList.remove('active');
                localStorage.setItem('productView', 'list');
            });
            
            // Load saved view preference
            const savedView = localStorage.getItem('productView') || 'grid';
            if (savedView === 'list') {
                listViewBtn.click();
            }

            // Sort Functionality
            const sortSelect = document.getElementById('sortSelect');
            sortSelect.addEventListener('change', function() {
                const productCards = Array.from(document.querySelectorAll('.product-card'));
                const container = document.getElementById('productsContainer');
                
                productCards.sort((a, b) => {
                    const value = this.value;
                    
                    if (value === 'name_asc') {
                        return a.dataset.name.localeCompare(b.dataset.name);
                    } else if (value === 'name_desc') {
                        return b.dataset.name.localeCompare(a.dataset.name);
                    } else if (value === 'price_low') {
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    } else if (value === 'price_high') {
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    } else if (value === 'newest') {
                        return new Date(b.dataset.date) - new Date(a.dataset.date);
                    }
                    return 0;
                });
                
                // Reorder products in DOM
                productCards.forEach(card => {
                    container.appendChild(card);
                });
            });

            // Quick View Modal
            const quickViewBtns = document.querySelectorAll('.quick-view-btn');
            const modal = document.getElementById('quickViewModal');
            const closeModal = document.querySelector('.close-modal');
            const modalContent = document.getElementById('modalProductContent');
            
            quickViewBtns.forEach(btn => {
                btn.addEventListener('click', async () => {
                    const productId = btn.dataset.id;
                    
                    try {
                        // Show loading state
                        modalContent.innerHTML = `
                            <div style="text-align: center; padding: 40px;">
                                <div class="loading-spinner" style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #4caf50; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
                                <p>Loading product details...</p>
                            </div>
                        `;
                        
                        modal.style.display = 'flex';
                        
                        // In a real application, you would fetch product details from the server
                        // For now, we'll simulate with the current product data
                        const productCard = btn.closest('.product-card');
                        const productName = productCard.querySelector('.product-title').textContent;
                        const productCategory = productCard.querySelector('.product-category').textContent;
                        const productDescription = productCard.querySelector('.product-description').textContent;
                        const productPrice = productCard.querySelector('.current-price').textContent;
                        const productImage = productCard.querySelector('img').src;
                        
                        // Simulate API delay
                        await new Promise(resolve => setTimeout(resolve, 500));
                        
                        modalContent.innerHTML = `
                            <div class="modal-product-image">
                                <img src="${productImage}" alt="${productName}" style="width: 100%; border-radius: 10px;">
                            </div>
                            <div class="modal-product-info">
                                <div class="product-category">${productCategory}</div>
                                <h2 style="font-size: 28px; margin-bottom: 15px;">${productName}</h2>
                                <div class="product-price" style="font-size: 32px; color: #2e7d32; font-weight: 700; margin-bottom: 20px;">
                                    ${productPrice}
                                </div>
                                <p style="color: #666; line-height: 1.6; margin-bottom: 30px;">
                                    ${productDescription}
                                </p>
                                <div style="margin-bottom: 30px;">
                                    <h3 style="font-size: 18px; margin-bottom: 10px;">Product Details</h3>
                                    <ul style="color: #666; list-style: none; padding: 0;">
                                        <li style="padding: 5px 0; border-bottom: 1px solid #eee;">
                                            <i class="fas fa-leaf" style="color: #4caf50; margin-right: 10px;"></i>
                                            Fresh from Kenyan farms
                                        </li>
                                        <li style="padding: 5px 0; border-bottom: 1px solid #eee;">
                                            <i class="fas fa-truck" style="color: #4caf50; margin-right: 10px;"></i>
                                            Free delivery in Kahawa Wendani
                                        </li>
                                        <li style="padding: 5px 0; border-bottom: 1px solid #eee;">
                                            <i class="fas fa-shield-alt" style="color: #4caf50; margin-right: 10px;"></i>
                                            Hygienic packaging
                                        </li>
                                    </ul>
                                </div>
                                <div style="display: flex; gap: 15px;">
                                    <div style="flex: 1;">
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Quantity</label>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <button class="qty-btn" style="width: 40px; height: 40px; border: 2px solid #e0e0e0; background: white; border-radius: 8px; cursor: pointer;">-</button>
                                            <input type="number" value="1" min="1" max="10" style="width: 60px; text-align: center; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px;">
                                            <button class="qty-btn" style="width: 40px; height: 40px; border: 2px solid #e0e0e0; background: white; border-radius: 8px; cursor: pointer;">+</button>
                                        </div>
                                    </div>
                                    <div style="flex: 2;">
                                        <button class="add-to-cart-btn" style="width: 100%; padding: 15px; background: linear-gradient(135deg, #2e7d32, #4caf50); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                    } catch (error) {
                        modalContent.innerHTML = `
                            <div style="text-align: center; padding: 40px;">
                                <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #f44336; margin-bottom: 20px;"></i>
                                <h3>Error Loading Product</h3>
                                <p>Unable to load product details. Please try again.</p>
                            </div>
                        `;
                    }
                });
            });
            
            // Close modal
            closeModal.addEventListener('click', () => {
                modal.style.display = 'none';
            });
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Quantity controls in modal
            modalContent.addEventListener('click', (e) => {
                if (e.target.classList.contains('qty-btn')) {
                    const input = e.target.parentElement.querySelector('input');
                    let value = parseInt(input.value);
                    
                    if (e.target.textContent === '+') {
                        input.value = Math.min(value + 1, 10);
                    } else {
                        input.value = Math.max(value - 1, 1);
                    }
                }
            });

            // Add to cart from modal
            modalContent.addEventListener('click', (e) => {
                if (e.target.classList.contains('add-to-cart-btn')) {
                    const quantity = e.target.closest('.modal-product-info').querySelector('input').value;
                    alert(`Added ${quantity} item(s) to cart!`);
                    modal.style.display = 'none';
                }
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
        });
    </script>
</body>
</html>