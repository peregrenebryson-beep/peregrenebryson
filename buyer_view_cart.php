<?php include "../config/database.php"; session_start(); ?>

<h2>Your Cart</h2>

<table border="1" cellpadding="10">

<tr>
<th>Product</th>
<th>Price</th>
<th>Qty</th>
<th>Total</th>
<th>Action</th>
</tr>

<?php
$total = 0;

if(isset($_SESSION['cart'])){

foreach($_SESSION['cart'] as $id => $qty){

$product = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM products WHERE id=$id"));

$sub = $product['price'] * $qty;

$total += $sub;
?>

<tr>
<td><?php echo $product['product_name']; ?></td>
<td><?php echo $product['price']; ?></td>
<td><?php echo $qty; ?></td>
<td><?php echo $sub; ?></td>
<td>
<a href="remove_cart.php?id=<?php echo $id; ?>">Remove</a>
</td>
</tr>

<?php }} ?>

<tr>
<td colspan="3"><b>Total</b></td>
<td><b><?php echo $total; ?></b></td>
<td></td>
</tr>

</table>

<br>

<a href="checkout.php">Proceed to Checkout</a>