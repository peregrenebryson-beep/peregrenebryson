<?php

include "../includes/session_config.php";
include "../config/database.php";
include "../includes/csrf.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'seller'){
    header("Location: ../login.php");
    exit();
}

// Validate CSRF token
if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
    die("Invalid request");
}

// Validate input
if(empty($_POST['name']) || empty($_POST['category_id']) || empty($_POST['price']) || empty($_POST['stock'])){
    die("All required fields must be filled");
}

$seller_id = $_SESSION['id'];
$name = htmlspecialchars(trim($_POST['name']));
$category_id = intval($_POST['category_id']);
$description = htmlspecialchars(trim($_POST['description']));
$price = floatval($_POST['price']);
$stock = intval($_POST['stock']);

// Validate price and stock
if($price <= 0 || $stock < 0){
    die("Invalid price or stock value");
}

// Handle image upload
$image = "";
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['image']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if(in_array($ext, $allowed)){
        $new_name = uniqid() . '.' . $ext;
        $upload_path = '../uploads/' . $new_name;
        
        if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)){
            $image = $new_name;
        }
    }
}

// Insert product using prepared statement
$sql = "INSERT INTO products (seller_id, category_id, name, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iissdis", $seller_id, $category_id, $name, $description, $price, $stock, $image);

if(mysqli_stmt_execute($stmt)){
    header("Location: index.php");
    exit();
} else {
    die("Failed to add product");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
