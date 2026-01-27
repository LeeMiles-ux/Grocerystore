<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Uchumi Grocery</title>
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
        <h2>Our Products</h2>
        
        <div class="filter-section">
            <button class="filter-btn active" data-category="all">All Products</button>
            <button class="filter-btn" data-category="vegetables">Vegetables</button>
            <button class="filter-btn" data-category="fruits">Fruits</button>
            <button class="filter-btn" data-category="grains">Grains</button>
        </div>
        
        <div class="products-grid">
            <?php
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="product-card" data-category="' . $row['category'] . '">';
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
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Uchumi Grocery Store, Kahawa Wendani</p>
        </div>
    </footer>

    <script>
        // Filter products by category
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Add active class to clicked button
                button.classList.add('active');
                
                const category = button.getAttribute('data-category');
                const productCards = document.querySelectorAll('.product-card');
                
                productCards.forEach(card => {
                    if (category === 'all' || card.getAttribute('data-category') === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>