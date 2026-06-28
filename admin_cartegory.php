<?php include "includes/header.php"; ?>
<?php include "../config/database.php"; ?>

<h3>Categories</h3>

<!-- ADD CATEGORY FORM -->
<div class="card p-3 mb-3">
    <form method="POST">
        <div class="row">
            <div class="col-md-8">
                <input type="text" name="category_name" class="form-control" placeholder="Enter category name" required>
            </div>
            <div class="col-md-4">
                <button type="submit" name="add" class="btn btn-success w-100">Add Category</button>
            </div>
        </div>
    </form>
</div>

<?php
// INSERT CATEGORY
if(isset($_POST['add'])){
    $name = $_POST['category_name'];

    $sql = "INSERT INTO categories(category_name) VALUES('$name')";
    mysqli_query($conn, $sql);

    echo "<script>window.location='categories.php';</script>";
}
?>

<!-- DISPLAY CATEGORIES -->
<div class="card p-3">

<table class="table table-bordered">

<tr>
    <th>ID</th>
    <th>Category Name</th>
    <th>Action</th>
</tr>

<?php
$sql = "SELECT * FROM categories ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)){
?>

<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['category_name']; ?></td>
    <td>
        <a href="delete_category.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
    </td>
</tr>

<?php } ?>

</table>

</div>

<?php include "includes/footer.php"; ?>