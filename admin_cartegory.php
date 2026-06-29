<?php include "header.php"; ?>
<?php include "config/database.php"; ?>
<?php include "includes/csrf.php"; ?>
$csrf_token = generateCsrfToken();

<h3>Categories</h3>

<!-- ADD CATEGORY FORM -->
<div class="card p-3 mb-3">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
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
    if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
        die("Invalid request");
    }
    $name = htmlspecialchars(trim($_POST['category_name']));

    $sql = "INSERT INTO categories(name) VALUES(?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "<script>window.location='admin_cartegory.php';</script>";
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
    <td><?php echo htmlspecialchars($row['id']); ?></td>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td>
        <a href="admin_cartegory_delete.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-sm">Delete</a>
    </td>
</tr>

<?php } ?>

</table>

</div>

<?php include "footer.php"; ?>