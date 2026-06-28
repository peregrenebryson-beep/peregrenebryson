<?php

include "includes/session_config.php";
include "config/database.php";
include "includes/csrf.php";

// Validate CSRF token
if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
    die("Invalid request");
}

// Validate input
if(empty($_POST['fullname']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])){
    die("All fields are required");
}

$fullname = htmlspecialchars(trim($_POST['fullname']));
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$role = $_POST['role'];

// Validate email
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    die("Invalid email format");
}

// Validate role
if(!in_array($role, ['admin', 'seller', 'buyer'])){
    die("Invalid role");
}

// Password strength validation
if(strlen($password) < 8){
    die("Password must be at least 8 characters");
}

// Check if email already exists using prepared statement
$check = "SELECT id FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $check);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) > 0){
    die("Email already exists!");
} else {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $fullname, $email, $hashed_password, $role);
    
    if(mysqli_stmt_execute($stmt)){
        header("Location: login.php");
        exit();
    } else {
        die("Registration failed");
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
