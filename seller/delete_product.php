<?php

include "../includes/session_config.php";
include "../config/database.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'seller'){
    header("Location: ../login.php");
    exit();
}

// Validate ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid product ID");
}

$product_id = intval($_GET['id']);

// Verify product belongs to this seller
$stmt = mysqli_prepare($conn, "SELECT seller_id FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if(!$product || $product['seller_id'] != $_SESSION['id']){
    die("Unauthorized access");
}

// Delete product
$stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);

if(mysqli_stmt_execute($stmt)){
    header("Location: index.php");
    exit();
} else {
    die("Failed to delete product");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
