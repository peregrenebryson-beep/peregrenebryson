<?php

// Database setup script
$host = "localhost";
$user = "root";
$password = "";

// Connect without database first
$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS apfims";
if ($conn->query($sql) === TRUE) {
    echo "Database created or already exists.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select database
$conn->select_db("apfims");

// Check if users table exists and has issues
$check_table = "SHOW TABLES LIKE 'users'";
$result = $conn->query($check_table);

if ($result->num_rows > 0) {
    // Table exists, try to check if it's corrupted
    try {
        $conn->query("SELECT COUNT(*) FROM users");
        echo "Users table exists and is accessible.<br>";
    } catch (Exception $e) {
        // Table exists but has issues, try to recreate
        echo "Users table exists but has issues. Recreating...<br>";
        $conn->query("DROP TABLE IF EXISTS users");
    }
}

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'seller', 'buyer') NOT NULL DEFAULT 'buyer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Check if admin user exists
$check = "SELECT * FROM users WHERE email = 'admin@apfims.com'";
$result = $conn->query($check);

if ($result->num_rows == 0) {
    // Insert admin user with password: admin123
    $hashed_password = password_hash("admin123", PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $role);
    $fullname = "Admin User";
    $email = "admin@apfims.com";
    $role = "admin";
    
    if ($stmt->execute()) {
        echo "Admin user created successfully.<br>";
        echo "Email: admin@apfims.com<br>";
        echo "Password: admin123<br>";
    } else {
        die("Error creating admin user: " . $stmt->error);
    }
    $stmt->close();
} else {
    echo "Admin user already exists.<br>";
}

echo "<br><strong>Setup complete!</strong> <a href='login.php'>Go to Login</a>";

$conn->close();

?>
