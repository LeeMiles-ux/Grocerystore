<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    
    // Get product details from database
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Add product to cart or update quantity if already exists
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$product_id] = array(
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
                'image' => $product['image'] ?? 'placeholder.jpg',
                'category' => $product['category'] ?? 'Grocery'
            );
        }
        
        // Calculate cart count
        $cart_count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += $item['quantity'];
        }
        
        echo json_encode([
            'success' => true,
            'cart_count' => $cart_count,
            'message' => 'Product added to cart successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found!'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method!'
    ]);
}
?>