<?php
include '../config.php';
requireAdmin();

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = (int)$_GET['id'];

// Get order info
$stmt = $conn->prepare("SELECT o.*, u.username, u.email, u.phone FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Get order items
$stmt = $conn->prepare("SELECT oi.*, p.name, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?php echo $order_id; ?> - Uchumi Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2e7d32; --secondary: #ff9800; --dark: #1b5e20; --light: #f1f8e9; --white: #ffffff; --gray: #f4f4f4; --text: #333; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--gray); display: flex; }
        .sidebar { width: 250px; height: 100vh; background: var(--dark); color: white; padding: 20px; position: fixed; }
        .sidebar h2 { margin-bottom: 30px; text-align: center; }
        .sidebar ul { list-style: none; }
        .sidebar ul li { margin-bottom: 15px; }
        .sidebar ul li a { color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 5px; transition: 0.3s; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: var(--primary); }
        .main-content { margin-left: 250px; flex: 1; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .content-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px; cursor: pointer; border: none; display: inline-block; }
        .btn-primary { background: var(--primary); color: white; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-group { margin-bottom: 10px; }
        .info-group label { font-weight: 600; color: #666; display: block; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Uchumi Admin</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="../index.php"><i class="fas fa-globe"></i> View Site</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Order Details #<?php echo $order_id; ?></h1>
            <a href="orders.php" class="btn btn-primary">Back to Orders</a>
        </div>
        
        <div class="grid">
            <div class="content-card">
                <h3>Customer Information</h3>
                <div style="margin-top: 15px;">
                    <div class="info-group">
                        <label>Username:</label>
                        <span><?php echo $order['username'] ?? 'Guest'; ?></span>
                    </div>
                    <div class="info-group">
                        <label>Email:</label>
                        <span><?php echo $order['email'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-group">
                        <label>Phone:</label>
                        <span><?php echo $order['phone'] ?? 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="content-card">
                <h3>Order Summary</h3>
                <div style="margin-top: 15px;">
                    <div class="info-group">
                        <label>Status:</label>
                        <span style="text-transform: uppercase; font-weight: 600; color: var(--primary);"><?php echo $order['status']; ?></span>
                    </div>
                    <div class="info-group">
                        <label>Date:</label>
                        <span><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="info-group">
                        <label>Shipping Address:</label>
                        <span><?php echo $order['shipping_address']; ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="content-card">
            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td style="display: flex; align-items: center; gap: 10px;">
                            <img src="../images/products/<?php echo $item['image'] ?? 'product-placeholder.jpg'; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;" onerror="this.src='../images/placeholders/product-placeholder.jpg'">
                            <?php echo $item['name']; ?>
                        </td>
                        <td>KSh <?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>KSh <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align: right;">Total Amount:</th>
                        <th>KSh <?php echo number_format($order['total_amount'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>
