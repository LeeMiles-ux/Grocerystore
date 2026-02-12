<?php
include '../config.php';
requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = '';

// Handle Product Deletion
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Product deleted successfully!";
    }
    $action = 'list';
}

// Handle Product Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $category = sanitizeInput($_POST['category']);
    $price = (float)$_POST['price'];
    $description = sanitizeInput($_POST['description']);
    $status = sanitizeInput($_POST['status']);
    $unit = sanitizeInput($_POST['unit']);
    
    $image_url = $_POST['existing_image'] ?? 'images/products/product-placeholder.jpg';
    
    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../images/products/";
        $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $name) . "." . $file_ext;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = "images/products/" . $file_name;
        }
    }
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, description=?, image_url=?, status=?, unit=? WHERE id=?");
        $stmt->bind_param("ssdssssi", $name, $category, $price, $description, $image_url, $status, $unit, $id);
        if ($stmt->execute()) {
            $message = "Product updated successfully!";
        }
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, description, image_url, status, unit) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssss", $name, $category, $price, $description, $image_url, $status, $unit);
        if ($stmt->execute()) {
            $message = "Product added successfully!";
        }
    }
    $action = 'list';
}

// Get product for editing
$edit_product = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $edit_product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
}

// Get all products
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Uchumi Admin</title>
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
        .content-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px; cursor: pointer; border: none; display: inline-block; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-danger { background: #e53935; color: white; }
        .btn-warning { background: var(--secondary); color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; background: #e8f5e9; color: #2e7d32; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Uchumi Admin</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="products.php" class="active"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="../index.php"><i class="fas fa-globe"></i> View Site</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Manage Products</h1>
            <a href="products.php?action=add" class="btn btn-primary">Add New Product</a>
        </div>
        
        <?php if($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="content-card">
            <?php if($action == 'add' || $action == 'edit'): ?>
                <h3><?php echo $action == 'edit' ? 'Edit Product' : 'Add New Product'; ?></h3>
                <form action="products.php" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                    <?php if($edit_product): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                        <input type="hidden" name="existing_image" value="<?php echo $edit_product['image_url']; ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Product Name</label>
                            <input type="text" name="name" required value="<?php echo $edit_product['name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" required>
                                <option value="Vegetables" <?php echo (isset($edit_product['category']) && $edit_product['category'] == 'Vegetables') ? 'selected' : ''; ?>>Vegetables</option>
                                <option value="Fruits" <?php echo (isset($edit_product['category']) && $edit_product['category'] == 'Fruits') ? 'selected' : ''; ?>>Fruits</option>
                                <option value="Groceries" <?php echo (isset($edit_product['category']) && $edit_product['category'] == 'Groceries') ? 'selected' : ''; ?>>Groceries</option>
                                <option value="Dairy" <?php echo (isset($edit_product['category']) && $edit_product['category'] == 'Dairy') ? 'selected' : ''; ?>>Dairy</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Price (KSh)</label>
                            <input type="number" step="0.01" name="price" required value="<?php echo $edit_product['price'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Unit (e.g. kg, bunch, piece)</label>
                            <input type="text" name="unit" required value="<?php echo $edit_product['unit'] ?? 'kg'; ?>">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="active" <?php echo (isset($edit_product['status']) && $edit_product['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo (isset($edit_product['status']) && $edit_product['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"><?php echo $edit_product['description'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Product Image</label>
                        <?php if(isset($edit_product['image_url'])): ?>
                            <img src="../<?php echo $edit_product['image_url']; ?>" style="width: 100px; margin-bottom: 10px; display: block;">
                        <?php endif; ?>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">Save Product</button>
                        <a href="products.php" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($product = $products->fetch_assoc()): ?>
                        <tr>
                            <td><img src="../<?php echo $product['image_url']; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['category']; ?></td>
                            <td>KSh <?php echo number_format($product['price'], 2); ?></td>
                            <td><span style="padding: 3px 8px; border-radius: 10px; font-size: 12px; background: <?php echo $product['status'] == 'active' ? '#e8f5e9' : '#ffebee'; ?>; color: <?php echo $product['status'] == 'active' ? '#2e7d32' : '#c62828'; ?>;"><?php echo ucfirst($product['status']); ?></span></td>
                            <td>
                                <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                <a href="products.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
