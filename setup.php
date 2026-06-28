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
    echo "Users table created or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Check if admin user exists
$check = "SELECT * FROM users WHERE email = 'admin@apfims.com'";
$result = $conn->query($check);

if ($result->num_rows == 0) {
    // Insert admin user with password: admin123
    $hashed_password = password_hash("admin123", PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (fullname, email, password, role) VALUES ('Admin User', 'admin@apfims.com', '$hashed_password', 'admin')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Admin user created successfully.<br>";
        echo "Email: admin@apfims.com<br>";
        echo "Password: admin123<br>";
    } else {
        die("Error creating admin user: " . $conn->error);
    }
} else {
    echo "Admin user already exists.<br>";
}

echo "<br><strong>Setup complete!</strong> <a href='login.php'>Go to Login</a>";

$conn->close();

?>
