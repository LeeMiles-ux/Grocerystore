<?php
// add_featured_column.php
// Run this only if you already have products table

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uchumi_grocery";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add featured column if not exists
$sql = "SHOW COLUMNS FROM products LIKE 'featured'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $sql = "ALTER TABLE products ADD COLUMN featured BOOLEAN DEFAULT FALSE";
    
    if ($conn->query($sql) === TRUE) {
        echo "Added 'featured' column to products table<br>";
        
        // Set some products as featured
        $sql = "UPDATE products SET featured = 1 WHERE id IN (1, 2, 3, 5, 6, 7, 14)";
        if ($conn->query($sql) === TRUE) {
            echo "Set some products as featured<br>";
        }
    } else {
        echo "Error adding column: " . $conn->error . "<br>";
    }
} else {
    echo "'featured' column already exists<br>";
}

// Also check for other missing columns
$columns_to_add = [
    'discount' => "ALTER TABLE products ADD COLUMN discount DECIMAL(5,2) DEFAULT 0",
    'unit' => "ALTER TABLE products ADD COLUMN unit VARCHAR(20) DEFAULT 'per kg'",
    'image' => "ALTER TABLE products ADD COLUMN image VARCHAR(255) DEFAULT 'placeholder.jpg'",
    'category_id' => "ALTER TABLE products ADD COLUMN category_id INT DEFAULT 1"
];

foreach ($columns_to_add as $column => $sql) {
    $check_sql = "SHOW COLUMNS FROM products LIKE '$column'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows == 0) {
        if ($conn->query($sql) === TRUE) {
            echo "Added '$column' column to products table<br>";
        } else {
            echo "Error adding '$column' column: " . $conn->error . "<br>";
        }
    }
}

echo "<br>Database update complete!<br>";
$conn->close();
?>