<?php 
include 'config.php';

// Get cart items with product details from database
$cart_items = [];
$total = 0;
$item_count = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $subtotal = $item['quantity'] * $item['price'];
            $total += $subtotal;
            $item_count += $item['quantity'];
            
            $cart_items[] = array_merge($product, [
                'cart_quantity' => $item['quantity'],
                'subtotal' => $subtotal
            ]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Uchumi Grocery</title>
    
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

        /* Header (same as index.php) */
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

        /* Cart Page Specific Styles */
        .cart-page {
            padding: 60px 0;
            min-height: 60vh;
        }

        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 30px;
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

        .cart-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }

        @media (max-width: 992px) {
            .cart-container {
                grid-template-columns: 1fr;
            }
        }

        /* Cart Items */
        .cart-items {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        .cart-header {
            padding: 20px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: var(--white);
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 20px;
        }

        .cart-header h3 {
            font-weight: 600;
            font-size: 16px;
        }

        .cart-item {
            padding: 20px;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 20px;
            align-items: center;
            border-bottom: 1px solid #eee;
            transition: var(--transition);
        }

        .cart-item:hover {
            background: #f9f9f9;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-product {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .cart-product-img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
            background: #f5f5f5;
        }

        .cart-product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-product-info h4 {
            font-size: 16px;
            margin-bottom: 5px;
            color: var(--dark-text);
        }

        .cart-product-info .category {
            color: var(--light-green);
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .cart-price {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-green);
        }

        .cart-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .quantity-btn:hover {
            background: var(--primary-green);
            color: var(--white);
            border-color: var(--primary-green);
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
            font-size: 16px;
        }

        .cart-subtotal {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-text);
        }

        .cart-remove {
            color: var(--accent-red);
            cursor: pointer;
            transition: var(--transition);
        }

        .cart-remove:hover {
            transform: scale(1.1);
        }

        /* Cart Summary */
        .cart-summary {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow-light);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .summary-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            color: var(--dark-text);
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .summary-row.total {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-green);
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .checkout-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(46, 125, 50, 0.3);
        }

        .continue-shopping {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
            margin-top: 20px;
            transition: var(--transition);
        }

        .continue-shopping:hover {
            gap: 15px;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-cart-icon {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 30px;
        }

        .empty-cart h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: var(--dark-text);
        }

        .empty-cart p {
            color: var(--light-text);
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-primary {
            padding: 15px 40px;
            background: linear-gradient(135deg, var(--accent-orange), #ffb74d);
            color: var(--white);
            border: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 152, 0, 0.3);
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
            .cart-header, .cart-item {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .cart-header {
                display: none;
            }
            
            .cart-product {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .page-title {
                font-size: 28px;
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
                        <span class="cart-count" id="cartCount">
                            <?php 
                            $cart_count = 0;
                            if (isset($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $cart_count += $item['quantity'];
                                }
                            }
                            echo $cart_count;
                            ?>
                        </span>
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="order.php" class="action-item">
                            <i class="fas fa-box action-icon"></i>
                            <span class="action-text">Orders</span>
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

    <!-- Cart Page -->
    <section class="cart-page">
        <div class="container">
            <h1 class="page-title animate__animated animate__fadeIn">
                Shopping Cart 
                <?php if ($item_count > 0): ?>
                    <span style="font-size: 18px; color: var(--light-text);">(<?php echo $item_count; ?> items)</span>
                <?php endif; ?>
            </h1>
            
            <?php if (empty($cart_items)): ?>
                <div class="empty-cart animate__animated animate__fadeIn">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added any items to your cart yet. Start shopping to fill it with fresh produce!</p>
                    <a href="product.php" class="btn-primary">
                        <i class="fas fa-shopping-bag"></i> Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="cart-container animate__animated animate__fadeIn">
                    <div class="cart-items">
                        <div class="cart-header">
                            <h3>PRODUCT</h3>
                            <h3>PRICE</h3>
                            <h3>QUANTITY</h3>
                            <h3>SUBTOTAL</h3>
                        </div>
                        
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item" data-id="<?php echo $item['id']; ?>">
                                <div class="cart-product">
                                    <div class="cart-product-img">
                                        <img src="images/products/<?php echo $item['image'] ?? 'placeholder.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             onerror="this.src='https://via.placeholder.com/150'">
                                    </div>
                                    <div class="cart-product-info">
                                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <p class="category"><?php echo $item['category'] ?? 'Grocery'; ?></p>
                                    </div>
                                </div>
                                
                                <div class="cart-price">
                                    KSh <?php echo number_format($item['price'], 2); ?>
                                </div>
                                
                                <div class="cart-quantity">
                                    <button class="quantity-btn minus-btn" data-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" 
                                           class="quantity-input" 
                                           value="<?php echo $item['cart_quantity']; ?>" 
                                           min="1" 
                                           max="99"
                                           data-id="<?php echo $item['id']; ?>"
                                           data-price="<?php echo $item['price']; ?>">
                                    <button class="quantity-btn plus-btn" data-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                
                                <div class="cart-subtotal" id="subtotal-<?php echo $item['id']; ?>">
                                    KSh <?php echo number_format($item['subtotal'], 2); ?>
                                </div>
                                
                                <div class="cart-remove" onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <h3 class="summary-title">Order Summary</h3>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="cartSubtotal">KSh <?php echo number_format($total, 2); ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span id="deliveryFee">
                                <?php if ($total >= 1000): ?>
                                    FREE
                                <?php else: ?>
                                    KSh 200
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Estimated Tax</span>
                            <span>KSh <?php echo number_format($total * 0.16, 2); ?></span>
                        </div>
                        
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="cartTotal">
                                KSh <?php 
                                $delivery = ($total >= 1000) ? 0 : 200;
                                $tax = $total * 0.16;
                                echo number_format($total + $delivery + $tax, 2); 
                                ?>
                            </span>
                        </div>
                        
                        <button class="checkout-btn" onclick="proceedToCheckout()">
                            <i class="fas fa-lock"></i> Proceed to Checkout
                        </button>
                        
                        <a href="product.php" class="continue-shopping">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                        
                        <div style="margin-top: 25px; padding: 15px; background: #f8f9fa; border-radius: 10px; font-size: 14px; color: var(--light-text);">
                            <i class="fas fa-info-circle" style="color: var(--primary-green);"></i>
                            <span>Free delivery for orders above KSh 1,000</span>
                        </div>
                    </div>
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
                        <li><a href="cart.php" style="color: #999; text-decoration: none; transition: var(--transition); display: block; padding: 5px 0;">
                            <i class="fas fa-chevron-right" style="margin-right: 10px;"></i> Cart
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
            // Update cart count in header
            updateCartCount();
            
            // Quantity button functionality
            document.querySelectorAll('.quantity-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
                    let quantity = parseInt(input.value);
                    
                    if (this.classList.contains('minus-btn')) {
                        if (quantity > 1) {
                            quantity--;
                        }
                    } else if (this.classList.contains('plus-btn')) {
                        if (quantity < 99) {
                            quantity++;
                        }
                    }
                    
                    input.value = quantity;
                    updateCartQuantity(productId, quantity);
                });
            });
            
            // Input change event
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const productId = this.dataset.id;
                    let quantity = parseInt(this.value);
                    
                    if (quantity < 1) quantity = 1;
                    if (quantity > 99) quantity = 99;
                    
                    this.value = quantity;
                    updateCartQuantity(productId, quantity);
                });
            });
        });
        
        function updateCartQuantity(productId, quantity) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update subtotal for this item
                    const price = parseFloat(document.querySelector(`.quantity-input[data-id="${productId}"]`).dataset.price);
                    const subtotal = price * quantity;
                    document.getElementById(`subtotal-${productId}`).textContent = 'KSh ' + subtotal.toFixed(2);
                    
                    // Update cart summary
                    updateCartSummary(data.total, data.item_count);
                    updateCartCount();
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        function removeFromCart(productId) {
            if (confirm('Remove this item from cart?')) {
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove item from DOM
                        document.querySelector(`.cart-item[data-id="${productId}"]`).remove();
                        
                        // Update cart summary
                        updateCartSummary(data.total, data.item_count);
                        updateCartCount();
                        
                        // If cart is empty, reload page to show empty cart message
                        if (data.item_count === 0) {
                            setTimeout(() => location.reload(), 500);
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
        
        function updateCartSummary(total, itemCount) {
            document.getElementById('cartSubtotal').textContent = 'KSh ' + parseFloat(total).toFixed(2);
            
            // Calculate delivery
            const deliveryFee = total >= 1000 ? 0 : 200;
            document.getElementById('deliveryFee').textContent = deliveryFee === 0 ? 'FREE' : 'KSh ' + deliveryFee.toFixed(2);
            
            // Calculate tax (16% VAT)
            const tax = total * 0.16;
            
            // Calculate total
            const grandTotal = parseFloat(total) + deliveryFee + tax;
            document.getElementById('cartTotal').textContent = 'KSh ' + grandTotal.toFixed(2);
        }
        
        function updateCartCount() {
            // This would be updated via AJAX response
            // For now, we'll calculate from visible items
            let totalCount = 0;
            document.querySelectorAll('.quantity-input').forEach(input => {
                totalCount += parseInt(input.value);
            });
            
            document.getElementById('cartCount').textContent = totalCount;
        }
        
        function proceedToCheckout() {
            <?php if (isLoggedIn()): ?>
                window.location.href = 'checkout.php';
            <?php else: ?>
                alert('Please login to proceed to checkout');
                window.location.href = 'login.php?redirect=checkout';
            <?php endif; ?>
        }
    </script>
</body>
</html>