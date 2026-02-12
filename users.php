<?php
include '../config.php';
requireAdmin();

$message = '';

// Handle Role Update
if (isset($_POST['update_role'])) {
    $user_id = (int)$_POST['user_id'];
    $role = sanitizeInput($_POST['role']);
    
    // Prevent self-demotion
    if ($user_id == $_SESSION['user_id'] && $role != 'admin') {
        $message = "Error: You cannot demote yourself!";
    } else {
        $stmt = $conn->prepare("UPDATE users SET user_role = ? WHERE id = ?");
        $stmt->bind_param("si", $role, $user_id);
        if ($stmt->execute()) {
            $message = "User role updated successfully!";
        }
    }
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Uchumi Admin</title>
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
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; }
        .btn { padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px; cursor: pointer; border: none; }
        .btn-primary { background: var(--primary); color: white; }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; background: #e8f5e9; color: #2e7d32; }
        select { padding: 5px; border-radius: 5px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Uchumi Admin</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="../index.php"><i class="fas fa-globe"></i> View Site</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Manage Users</h1>
        </div>
        
        <?php if($message): ?>
            <div class="alert" style="<?php echo strpos($message, 'Error') !== false ? 'background: #ffebee; color: #c62828;' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="content-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['full_name']; ?></td>
                        <td>
                            <form action="users.php" method="POST" style="display: flex; gap: 5px;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <select name="role">
                                    <option value="user" <?php echo $user['user_role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?php echo $user['user_role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <button type="submit" name="update_role" class="btn btn-primary">Update</button>
                            </form>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
