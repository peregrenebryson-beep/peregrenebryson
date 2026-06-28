<?php
session_start();

$id = $_GET['id'];

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