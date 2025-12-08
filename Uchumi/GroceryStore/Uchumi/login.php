<?php include 'config.php'; 

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Process login form
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        // Check user credentials
        $stmt = $conn->prepare("SELECT id, username, password, full_name FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password (in a real application, use password_verify)
            if ($password === $user['password']) { // For demo purposes only - in production, use hashed passwords
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                
                // Redirect to homepage
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "User not found";
        }
    } else {
        $error = "Please fill in all fields";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Uchumi Grocery</title>
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
                    <?php if (isLoggedIn()): ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="active">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="auth-form">
            <h2>Login to Your Account</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="username">Username or Email:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Uchumi Grocery Store, Kahawa Wendani</p>
        </div>
    </footer>
</body>
</html>