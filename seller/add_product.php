<?php

include "../includes/session_config.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'seller'){
    header("Location: ../login.php");
    exit();
}

include "../config/database.php";
include "../includes/csrf.php";
$csrf_token = generateCsrfToken();

// Get categories
$stmt = mysqli_prepare($conn, "SELECT id, name FROM categories");
mysqli_stmt_execute($stmt);
$categories = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product - APFIMS</title>
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
                        <h4>Add New Product</h4>
                    </div>
                    <div class="card-body">
                        <form action="add_product_process.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="mb-3">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label>Category</label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php while($cat = mysqli_fetch_assoc($categories)){ ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label>Price (TZS)</label>
                                <input type="number" name="price" class="form-control" step="0.01" required>
                            </div>
                            
                            <div class="mb-3">
                                <label>Stock Quantity</label>
                                <input type="number" name="stock" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label>Product Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            
                            <button class="btn btn-success w-100">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php mysqli_stmt_close($stmt); ?>
