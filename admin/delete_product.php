<?php

include "../includes/session_config.php";
include "../config/database.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

// Validate ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid product ID");
}

$product_id = intval($_GET['id']);

// Delete product
$stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);

if(mysqli_stmt_execute($stmt)){
    header("Location: products.php");
    exit();
} else {
    die("Failed to delete product");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
