<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uchumi Grocery - Kahawa Wendani</title>
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
                        <?php if (isLoggedIn()): ?>
                        <li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                        <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                        <?php endif; ?>
                  </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="hero">
            <h2>Fresh Groceries Delivered to Your Doorstep</h2>
            <p>Best quality vegetables, fruits and groceries in Kahawa Wendani</p>
            <a href="product.php" class="btn">Shop Now</a>
        </section>

        <section class="featured-products">
            <h2>Popular Items</h2>
            <div class="products-grid">
                <?php
                $sql = "SELECT * FROM products LIMIT 4";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="product-card">';
                        echo '<img src="images/' . $row['image'] . '" alt="' . $row['name'] . '">';
                        echo '<h3>' . $row['name'] . '</h3>';
                        echo '<p class="price">KSh ' . $row['price'] . '</p>';
                        echo '<form method="post" action="add_to_cart.php">';
                        echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
                        echo '<button type="submit" class="btn">Add to Cart</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>No products available</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Uchumi Grocery Store, Kahawa Wendani</p>
        </div>
    </footer>
</body>
</html>