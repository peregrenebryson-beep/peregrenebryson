<?php
include "includes/session_config.php";

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid product ID");
}

$id = intval($_GET['id']);

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

// Add product to cart
if(isset($_SESSION['cart'][$id])){
    $_SESSION['cart'][$id]++;
} else {
    $_SESSION['cart'][$id] = 1;
}

header("Location: view_cart.php");
exit();
?>