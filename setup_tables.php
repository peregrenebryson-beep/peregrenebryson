<?php

include "config/database.php";

// Check if tables already exist - if so, skip creation
$check_tables = "SHOW TABLES LIKE 'categories'";
$result = $conn->query($check_tables);

if ($result->num_rows > 0) {
    echo "Tables already exist. Skipping creation.<br>";
    echo "If you want to recreate tables, please drop them manually first.<br>";
    echo "<br><strong>Setup complete!</strong> <a href='login.php'>Go to Login</a>";
    exit();
}

// Disable foreign key checks for table creation
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Create categories table
$sql = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Categories table created successfully.<br>";
} else {
    echo "Error creating categories table: " . $conn->error . "<br>";
}

// Create products table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Products table created successfully.<br>";
} else {
    echo "Error creating products table: " . $conn->error . "<br>";
}

// Create orders table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Orders table created successfully.<br>";
} else {
    echo "Error creating orders table: " . $conn->error . "<br>";
}

// Create order_items table
$sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Order items table created successfully.<br>";
} else {
    echo "Error creating order items table: " . $conn->error . "<br>";
}

// Insert default categories
$check = "SELECT * FROM categories WHERE id = 1";
$result = $conn->query($check);
if ($result->num_rows == 0) {
    $categories = [
        ['Electronics', 'Electronic devices and gadgets'],
        ['Clothing', 'Fashion and apparel'],
        ['Food & Beverages', 'Food items and drinks'],
        ['Home & Garden', 'Home improvement and garden items'],
        ['Sports', 'Sports equipment and accessories']
    ];
    
    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    foreach ($categories as $cat) {
        $name = $cat[0];
        $description = $cat[1];
        $stmt->bind_param("ss", $name, $description);
        $stmt->execute();
    }
    $stmt->close();
    echo "Default categories inserted.<br>";
}

echo "<br><strong>Database tables setup complete!</strong> <a href='login.php'>Go to Login</a>";

?>
