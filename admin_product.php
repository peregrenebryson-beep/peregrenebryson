<?php include "includes/header.php"; ?>
<?php include "../config/database.php"; ?>

<h3>Products (Mazao & Pembejeo)</h3>

<!-- ADD PRODUCT FORM -->
<div class="card p-3 mb-3">

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-3">
<input type="text" name="product_name" class="form-control" placeholder="Product Name" required>
</div>

<div class="col-md-2">
<input type="number" name="price" class="form-control" placeholder="Price" required>
</div>

<div class="col-md-2">
<input type="number" name="quantity" class="form-control" placeholder="Qty" required>
</div>

<div class="col-md-3">
<select name="category_id" class="form-control" required>
<option value="">Select Category</option>

<?php
$cat = mysqli_query($conn, "SELECT * FROM categories");
while($c = mysqli_fetch_assoc($cat)){
?>
<option value="<?php echo $c['id']; ?>">
<?php echo $c['category_name']; ?>
</option>
<?php } ?>

</select>
</div>

<div class="col-md-2">
<input type="file" name="image" class="form-control" required>
</div>

</div>

<br>

<button type="submit" name="add" class="btn btn-success">Add Product</button>

</form>

</div>

<?php

// INSERT PRODUCT
if(isset($_POST['add'])){

$name = $_POST['product_name'];
$price = $_POST['price'];
$qty = $_POST['quantity'];
$cat = $_POST['category_id'];

$image = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];

move_uploaded_file($tmp, "../uploads/".$image);

$sql = "INSERT INTO products(category_id, product_name, price, quantity, image)
VALUES('$cat','$name','$price','$qty','$image')";

mysqli_query($conn, $sql);

echo "<script>window.location='products.php';</script>";
}

?>

---

<!-- PRODUCTS TABLE -->

<div class="card p-3">

<table class="table table-bordered">

<tr>
<th>ID</th>
<th>Image</th>
<th>Name</th>
<th>Price</th>
<th>Qty</th>
<th>Category</th>
<th>Action</th>
</tr>

<?php

$sql = "SELECT p.*, c.category_name 
FROM products p 
JOIN categories c ON p.category_id = c.id
ORDER BY p.id DESC";

$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)){
?>

<tr>
<td><?php echo $row['id']; ?></td>

<td>
<img src="../uploads/<?php echo $row['image']; ?>" width="50">
</td>

<td><?php echo $row['product_name']; ?></td>
<td><?php echo $row['price']; ?></td>
<td><?php echo $row['quantity']; ?></td>
<td><?php echo $row['category_name']; ?></td>

<td>
<a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
</td>
<a href="../buyer/add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
Add to Cart
</a>
</tr>

<?php } ?>

</table>

</div>

<?php include "includes/footer.php"; ?>