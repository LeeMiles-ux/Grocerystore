<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);
    
    if ($quantity < 1) {
        $quantity = 1;
    }
    
    if ($quantity > 99) {
        $quantity = 99;
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        
        // Calculate new totals
        $total = 0;
        $item_count = 0;
        foreach ($_SESSION['cart'] as $id => $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
            $item_count += $item['quantity'];
        }
        
        // Calculate subtotal for this item
        $item_subtotal = $_SESSION['cart'][$product_id]['price'] * $quantity;
        
        echo json_encode([
            'success' => true,
            'total' => $total,
            'item_count' => $item_count,
            'item_subtotal' => $item_subtotal,
            'quantity' => $quantity
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>