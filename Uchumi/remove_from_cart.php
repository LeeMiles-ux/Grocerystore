<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        $product_id = $_POST['id'];
        
        if (isset($_SESSION['cart'][$product_id])) {
            // Remove the item from cart
            unset($_SESSION['cart'][$product_id]);
            
            // Calculate new total
            $total = 0;
            $item_count = 0;
            foreach ($_SESSION['cart'] as $id => $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
                $item_count += $item['quantity'];
            }
            
            echo json_encode([
                'success' => true,
                'total' => $total,
                'item_count' => $item_count,
                'message' => 'Item removed from cart successfully!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Item not found in cart!'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No product ID provided!'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method!'
    ]);
}
?>