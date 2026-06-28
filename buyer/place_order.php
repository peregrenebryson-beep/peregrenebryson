<?php

include "../includes/session_config.php";
include "../config/database.php";
include "../includes/csrf.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'buyer'){
    header("Location: ../login.php");
    exit();
}

// Validate CSRF token
if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
    die("Invalid request");
}

// Validate input
if(empty($_POST['shipping_address']) || empty($_POST['phone'])){
    die("Shipping address and phone are required");
}

$buyer_id = $_SESSION['id'];
$shipping_address = htmlspecialchars(trim($_POST['shipping_address']));
$phone = htmlspecialchars(trim($_POST['phone']));

// Get cart from localStorage equivalent (session for server-side)
$cart = isset($_POST['cart']) ? json_decode($_POST['cart'], true) : [];

if(empty($cart)){
    die("Cart is empty");
}

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Calculate total and verify stock
    $total_amount = 0;
    $order_items = [];
    
    foreach($cart as $item){
        $stmt = mysqli_prepare($conn, "SELECT id, name, price, stock FROM products WHERE id = ? AND status = 'active'");
        mysqli_stmt_bind_param($stmt, "i", $item['id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);
        
        if(!$product || $product['stock'] < $item['quantity']){
            throw new Exception("Product out of stock or not available");
        }
        
        $item_total = $product['price'] * $item['quantity'];
        $total_amount += $item_total;
        
        $order_items[] = [
            'product_id' => $product['id'],
            'quantity' => $item['quantity'],
            'price' => $product['price']
        ];
        
        mysqli_stmt_close($stmt);
    }
    
    // Create order
    $stmt = mysqli_prepare($conn, "INSERT INTO orders (buyer_id, total_amount, status, shipping_address, phone) VALUES (?, ?, 'pending', ?, ?)");
    mysqli_stmt_bind_param($stmt, "idss", $buyer_id, $total_amount, $shipping_address, $phone);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    
    // Add order items and update stock
    foreach($order_items as $item){
        // Insert order item
        $stmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Update stock
        $stmt = mysqli_prepare($conn, "UPDATE products SET stock = stock - ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $item['quantity'], $item['product_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    mysqli_commit($conn);
    
    // Clear cart
    echo json_encode(['success' => true, 'order_id' => $order_id]);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

mysqli_close($conn);

?>
