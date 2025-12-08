<?php
// simple_register.php
session_start();
include 'config.php';

echo "<h2>Simple Registration Test</h2>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = "testuser_" . rand(100, 999);
    $email = "test" . rand(100, 999) . "@test.com";
    $password = "password123";
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Try to insert
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
        if ($stmt->execute()) {
            echo "✅ Simple registration successful!<br>";
            echo "User ID: " . $stmt->insert_id . "<br>";
            echo "Username: $username<br>";
            echo "Email: $email<br>";
        } else {
            echo "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "❌ Prepare error: " . $conn->error;
    }
    
    // Show all users
    echo "<h3>Current users in database:</h3>";
    $result = $conn->query("SELECT id, username, email FROM users");
    if ($result->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Email</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['id'] . "</td><td>" . $row['username'] . "</td><td>" . $row['email'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No users found.";
    }
} else {
    echo '<form method="POST">
        <input type="submit" value="Test Simple Registration">
    </form>';
}

$conn->close();
?>