<?php

include "includes/session_config.php";
include "config/database.php";
include "includes/csrf.php";

// Validate CSRF token
if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
    die("Invalid request");
}

// Validate input
if(empty($_POST['email']) || empty($_POST['password'])){
    die("Email and password are required");
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    die("Invalid email format");
}

// Use prepared statement to prevent SQL injection
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) > 0){
    $user = mysqli_fetch_assoc($result);
    
    if(password_verify($password, $user['password'])){
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        $_SESSION['id'] = $user['id'];
        $_SESSION['fullname'] = htmlspecialchars($user['fullname']);
        $_SESSION['role'] = $user['role'];
        
        header("Location: dashboard.php");
        exit();
    } else {
        die("Wrong Password");
    }
} else {
    die("User Not Found");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>