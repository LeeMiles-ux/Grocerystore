<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Uchumi Grocery</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Uchumi Grocery Store</h1>
            <p>Kahawa Wendani</p>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="product.php">Products</a></li>
                    <li><a href="cart.php">Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
                    <li><a href="order.php">Orders</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Checkout</h2>
        
        <?php
        if (empty($_SESSION['cart'])) {
            echo '<p>Your cart is empty. <a href="product.php">Browse products</a></p>';
        } else {
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            }
            
            echo '<p><strong>Order Total: KSh ' . $total . '</strong></p>';
            ?>
            
            <form method="post" action="process_order.php" class="checkout-form">
                <h3>Customer Information</h3>
                
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Delivery Address:</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                
                <h3>Payment Information</h3>
                <p>For demonstration purposes only. No real payment processing.</p>
                
                <div class="form-group">
                    <label for="card_number">Card Number:</label>
                    <input type="text" id="card_number" name="card_number" value="4242 4242 4242 4242" readonly>
                </div>
                
                <div class="form-group">
                    <label for="expiry">Expiry Date:</label>
                    <input type="text" id="expiry" name="expiry" value="12/25" readonly>
                </div>
                
                <div class="form-group">
                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" value="123" readonly>
                </div>
                
                <button type="submit" class="btn btn-primary">Place Order</button>
            </form>
            <?php
        }
        ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Uchumi Grocery Store, Kahawa Wendani</p>
        </div>
    </footer>
</body>
</html>