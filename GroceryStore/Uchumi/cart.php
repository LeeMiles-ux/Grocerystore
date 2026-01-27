<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Uchumi Grocery</title>
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
        <h2>Your Shopping Cart</h2>
        
        <?php
        if (empty($_SESSION['cart'])) {
            echo '<p>Your cart is empty. <a href="product.php">Browse products</a></p>';
        } else {
            $total = 0;
            echo '<table class="cart-table">';
            echo '<tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr>';
            
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
                
                echo '<tr>';
                echo '<td>' . $item['name'] . '</td>';
                echo '<td>KSh ' . $item['price'] . '</td>';
                echo '<td>' . $item['quantity'] . '</td>';
                echo '<td>KSh ' . $subtotal . '</td>';
                echo '<td><a href="remove_from_cart.php?id=' . $product_id . '">Remove</a></td>';
                echo '</tr>';
            }
            
            echo '<tr><td colspan="3" style="text-align: right;"><strong>Total:</strong></td><td colspan="2">KSh ' . $total . '</td></tr>';
            echo '</table>';
            
            echo '<div class="cart-actions">';
            echo '<a href="product.php" class="btn">Continue Shopping</a>';
            echo '<a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>';
            echo '</div>';
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