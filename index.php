<?php
include '../config.php';
requireAdmin();

// Get stats
$product_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$user_count = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$order_count = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];

// Get recent products
$recent_products = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Uchumi Grocery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2e7d32;
            --secondary: #ff9800;
            --dark: #1b5e20;
            --light: #f1f8e9;
            --white: #ffffff;
            --gray: #f4f4f4;
            --text: #333;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--gray); display: flex; }
        
        /* Sidebar */
        .sidebar { width: 250px; height: 100vh; background: var(--dark); color: white; padding: 20px; position: fixed; }
        .sidebar h2 { margin-bottom: 30px; text-align: center; }
        .sidebar ul { list-style: none; }
        .sidebar ul li { margin-bottom: 15px; }
        .sidebar ul li a { color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 5px; transition: 0.3s; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: var(--primary); }
        
        /* Main Content */
        .main-content { margin-left: 250px; flex: 1; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 20px; }
        .stat-card i { font-size: 40px; color: var(--primary); }
        .stat-info h3 { font-size: 24px; }
        .stat-info p { color: #666; }
        
        .recent-table { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .recent-table h3 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px; cursor: pointer; border: none; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-danger { background: #e53935; color: white; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Uchumi Admin</h2>
        <ul>
            <li><a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="../index.php"><i class="fas fa-globe"></i> View Site</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Dashboard Overview</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['username']; ?></span>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <div class="stat-info">
                    <h3><?php echo $product_count; ?></h3>
                    <p>Products</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-info">
                    <h3><?php echo $user_count; ?></h3>
                    <p>Users</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <div class="stat-info">
                    <h3><?php echo $order_count; ?></h3>
                    <p>Orders</p>
                </div>
            </div>
        </div>
        
        <div class="recent-table">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Recently Added Products</h3>
                <a href="products.php?action=add" class="btn btn-primary">Add New Product</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($product = $recent_products->fetch_assoc()): ?>
                    <tr>
                        <td><img src="../<?php echo $product['image_url']; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['category']; ?></td>
                        <td>KSh <?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['status']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
