<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get customer information
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    // Calculate total
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
    }
    
    // Insert order into database
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, total_amount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $name, $phone, $address, $total);
    $stmt->execute();
    
    $order_id = $stmt->insert_id;
    
    // Insert order items
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['price']);
        $stmt->execute();
    }
    
    // Clear the cart
    $_SESSION['cart'] = array();
    
    // Redirect to order confirmation page
    header("Location: order_confirmation.php?id=" . $order_id);
    exit();
} else {
    header("Location: cart.php");
    exit();
}
?>