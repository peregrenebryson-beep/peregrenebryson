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
if(empty($_POST['product_id']) || empty($_POST['name']) || empty($_POST['category_id']) || empty($_POST['price']) || empty($_POST['stock'])){
    die("All required fields must be filled");
}

$product_id = intval($_POST['product_id']);
$seller_id = $_SESSION['id'];
$name = htmlspecialchars(trim($_POST['name']));
$category_id = intval($_POST['category_id']);
$description = htmlspecialchars(trim($_POST['description']));
$price = floatval($_POST['price']);
$stock = intval($_POST['stock']);
$status = $_POST['status'];

// Verify product belongs to this seller
$stmt = mysqli_prepare($conn, "SELECT seller_id, image FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if(!$product || $product['seller_id'] != $seller_id){
    die("Unauthorized access");
}

// Handle image upload
$image = $product['image'];
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['image']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if(in_array($ext, $allowed)){
        $new_name = uniqid() . '.' . $ext;
        $upload_path = '../uploads/' . $new_name;
        
        if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)){
            // Delete old image
            if($product['image'] && file_exists('../uploads/' . $product['image'])){
                unlink('../uploads/' . $product['image']);
            }
            $image = $new_name;
        }
    }
}

// Update product
$sql = "UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, stock = ?, status = ?, image = ? WHERE id = ? AND seller_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "issdisisi", $category_id, $name, $description, $price, $stock, $status, $image, $product_id, $seller_id);

if(mysqli_stmt_execute($stmt)){
    header("Location: index.php");
    exit();
} else {
    die("Failed to update product");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
