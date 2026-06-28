<?php

include "../includes/session_config.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'seller'){
    header("Location: ../login.php");
    exit();
}

include "../config/database.php";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Dashboard - APFIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">APFIMS - Seller</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>
    
    <div class="container mt-5">
        <h2>Welcome, <?php echo $_SESSION['fullname']; ?></h2>
        <p class="text-muted">Seller Dashboard</p>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>My Products</h4>
                        <a href="add_product.php" class="btn btn-success">Add New Product</a>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.seller_id = ? ORDER BY p.created_at DESC");
                        mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        
                        if(mysqli_num_rows($result) > 0){
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)){ ?>
                                    <tr>
                                        <td>
                                            <?php if($row['image']){ ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" width="50" height="50">
                                            <?php } else { ?>
                                            <span class="text-muted">No image</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                        <td>TZS <?php echo number_format($row['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['stock']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="edit_product.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="delete_product.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } else { ?>
                        <p class="text-muted">No products found. <a href="add_product.php">Add your first product</a></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
