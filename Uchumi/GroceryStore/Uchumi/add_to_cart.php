<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    
    // Get product details from database
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if ($product) {
        // Add product to cart or update quantity if already exists
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$product_id] = array(
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1
            );
        }
    }
}

// Redirect back to previous page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>