<?php

include "../includes/session_config.php";
include "../config/database.php";
include "../includes/csrf.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'seller'){
    header("Location: ../login.php");
    exit();
}

// Validate ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid product ID");
}

$product_id = intval($_GET['id']);

// Get product details
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ? AND seller_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $product_id, $_SESSION['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if(!$product){
    die("Product not found or unauthorized");
}

// Get categories
$cat_stmt = mysqli_prepare($conn, "SELECT id, name FROM categories");
mysqli_stmt_execute($cat_stmt);
$categories = mysqli_stmt_get_result($cat_stmt);

$csrf_token = generateCsrfToken();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - APFIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="index.php">APFIMS - Seller</a>
            <div>
                <a href="index.php" class="btn btn-light me-2">Dashboard</a>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4>Edit Product</h4>
                    </div>
                    <div class="card-body">
                        <form action="edit_product_process.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            
                            <div class="mb-3">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label>Category</label>
                                <select name="category_id" class="form-control" required>
                                    <?php while($cat = mysqli_fetch_assoc($categories)){ ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label>Price (TZS)</label>
                                <input type="number" name="price" class="form-control" step="0.01" value="<?php echo $product['price']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label>Stock Quantity</label>
                                <input type="number" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="active" <?php echo $product['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $product['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label>Product Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <?php if($product['image']){ ?>
                                <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" width="100" class="mt-2">
                                <?php } ?>
                            </div>
                            
                            <button class="btn btn-success w-100">Update Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php 
mysqli_stmt_close($stmt);
mysqli_stmt_close($cat_stmt);
?>
