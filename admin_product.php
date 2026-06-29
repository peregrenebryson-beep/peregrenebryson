<?php include "header.php"; ?>
<?php include "config/database.php"; ?>
<?php include "includes/csrf.php"; ?>
$csrf_token = generateCsrfToken();

<h3>Products (Mazao & Pembejeo)</h3>

<!-- ADD PRODUCT FORM -->
<div class="card p-3 mb-3">

<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

<div class="row">

<div class="col-md-3">
<input type="text" name="product_name" class="form-control" placeholder="Product Name" required>
</div>

<div class="col-md-2">
<input type="number" name="price" class="form-control" placeholder="Price" step="0.01" required>
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
<option value="<?php echo htmlspecialchars($c['id']); ?>">
<?php echo htmlspecialchars($c['name']); ?>
</option>
<?php } ?>

</select>
</div>

<div class="col-md-2">
<input type="file" name="image" class="form-control">
</div>

</div>

<br>

<button type="submit" name="add" class="btn btn-success">Add Product</button>

</form>

</div>

<?php

// INSERT PRODUCT
if(isset($_POST['add'])){
    if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
        die("Invalid request");
    }

    $name = htmlspecialchars(trim($_POST['product_name']));
    $price = floatval($_POST['price']);
    $qty = intval($_POST['quantity']);
    $cat = intval($_POST['category_id']);

    // Handle image upload
    $image = "";
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if(in_array($ext, $allowed)){
            $new_name = uniqid() . '.' . $ext;
            $upload_path = "uploads/" . $new_name;

            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)){
                $image = $new_name;
            }
        }
    }

    $sql = "INSERT INTO products(category_id, name, price, stock, image, seller_id)
VALUES(?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    $seller_id = $_SESSION['id'];
    mysqli_stmt_bind_param($stmt, "isdisi", $cat, $name, $price, $qty, $image, $seller_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "<script>window.location='admin_product.php';</script>";
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

$sql = "SELECT p.*, c.name as category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
ORDER BY p.id DESC";

$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)){
?>

<tr>
<td><?php echo htmlspecialchars($row['id']); ?></td>

<td>
<?php if($row['image']){ ?>
<img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" width="50">
<?php } else { ?>
<span class="text-muted">No image</span>
<?php } ?>
</td>

<td><?php echo htmlspecialchars($row['name']); ?></td>
<td>TZS <?php echo number_format($row['price'], 2); ?></td>
<td><?php echo htmlspecialchars($row['stock']); ?></td>
<td><?php echo htmlspecialchars($row['category_name']); ?></td>

<td>
<a href="admin_delete_product.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>

<?php } ?>

</table>

</div>

<?php include "footer.php"; ?>