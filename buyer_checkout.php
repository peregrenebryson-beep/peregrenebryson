<?php include "../config/database.php"; session_start(); ?>

<h2>Checkout</h2>

<form method="POST">

<input type="text" name="fullname" placeholder="Your Name" required><br><br>

<input type="text" name="phone" placeholder="Phone" required><br><br>

<button type="submit" name="place">Place Order</button>

</form>

<?php

if(isset($_POST['place'])){

$name = $_POST['fullname'];
$phone = $_POST['phone'];

// Save customer
mysqli_query($conn,"INSERT INTO customers(fullname,phone) VALUES('$name','$phone')");

$customer_id = mysqli_insert_id($conn);

// Create order
mysqli_query($conn,"INSERT INTO orders(customer_id,total_amount,order_status)
VALUES('$customer_id',0,'Pending')");

$order_id = mysqli_insert_id($conn);

$total = 0;

// Save order items
foreach($_SESSION['cart'] as $id => $qty){

$product = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM products WHERE id=$id"));

$price = $product['price'];
$sub = $price * $qty;

$total += $sub;

mysqli_query($conn,"INSERT INTO order_items(order_id,product_id,quantity,unit_price,subtotal)
VALUES('$order_id','$id','$qty','$price','$sub')");

// reduce stock
mysqli_query($conn,"UPDATE products SET quantity = quantity - $qty WHERE id=$id");

}

// update order total
mysqli_query($conn,"UPDATE orders SET total_amount=$total WHERE id=$order_id");

// clear cart
unset($_SESSION['cart']);

echo "Order placed successfully!";
}

?>