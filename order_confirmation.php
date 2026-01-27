<?php include 'config.php'; 
$order_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Uchumi Grocery</title>
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
        <div class="confirmation-message">
            <h2>Thank You for Your Order!</h2>
            <p>Your order has been placed successfully.</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
                <p><strong>Total Amount:</strong> KSh <?php echo $order['total_amount']; ?></p>
                <p><strong>Delivery Address:</strong> <?php echo $order['customer_address']; ?></p>
                <p><strong>Contact Phone:</strong> <?php echo $order['customer_phone']; ?></p>
            </div>
            
            <p>We will process your order and deliver it to you soon.</p>
            
            <div class="confirmation-actions">
                <a href="product.php" class="btn">Continue Shopping</a>
                <a href="index.php" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Uchumi Grocery Store, Kahawa Wendani</p>
        </div>
    </footer>
</body>
</html>