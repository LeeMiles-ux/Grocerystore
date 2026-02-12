<?php
// test_connection.php
session_start();
include 'config.php';

echo "<h2>Testing Database Connection</h2>";

// Test if connection is working
if ($conn->connect_error) {
    echo "❌ Connection failed: " . $conn->connect_error;
} else {
    echo "✅ Database connection successful!<br>";
    
    // Test if database exists
    $result = $conn->query("SELECT DATABASE()");
    $row = $result->fetch_array();
    echo "✅ Database: " . $row[0] . "<br>";
    
    // Test if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "✅ Users table exists!<br>";
        
        // Show table structure
        $result = $conn->query("DESCRIBE users");
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Users table does not exist!<br>";
    }
}

$conn->close();
?>