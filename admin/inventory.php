<?php
include 'layout.php';
include '../includes/csrf.php';
$csrf_token = generateCsrfToken();

// Handle stock update
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
        die("Invalid request");
    }
    
    $product_id = intval($_POST['product_id']);
    $stock = intval($_POST['stock']);
    
    $stmt = mysqli_prepare($conn, "UPDATE products SET stock = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $stock, $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: inventory.php");
    exit();
}

// Get low stock products (stock < 10)
$low_stock_stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name, u.fullname as seller_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN users u ON p.seller_id = u.id WHERE p.stock < 10 ORDER BY p.stock ASC");
mysqli_stmt_execute($low_stock_stmt);
$low_stock = mysqli_stmt_get_result($low_stock_stmt);

// Get all products
$stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name, u.fullname as seller_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN users u ON p.seller_id = u.id ORDER BY p.stock ASC");
mysqli_stmt_execute($stmt);
$products = mysqli_stmt_get_result($stmt);
?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-warehouse me-2"></i>Inventory</h2>
                    <div>
                        <select class="form-select d-inline-block" style="width: auto;">
                            <option>All Products</option>
                            <option>Low Stock</option>
                            <option>Out of Stock</option>
                        </select>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5>Total Products</h5>
                                <h3><?php 
                                    $total_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM products");
                                    mysqli_stmt_execute($total_stmt);
                                    $total = mysqli_stmt_get_result($total_stmt);
                                    $count = mysqli_fetch_assoc($total);
                                    echo $count['count'];
                                    mysqli_stmt_close($total_stmt);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5>In Stock</h5>
                                <h3><?php 
                                    $instock_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM products WHERE stock > 10");
                                    mysqli_stmt_execute($instock_stmt);
                                    $instock = mysqli_stmt_get_result($instock_stmt);
                                    $in_count = mysqli_fetch_assoc($instock);
                                    echo $in_count['count'];
                                    mysqli_stmt_close($instock_stmt);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <h5>Low Stock</h5>
                                <h3><?php 
                                    $low_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM products WHERE stock > 0 AND stock < 10");
                                    mysqli_stmt_execute($low_stmt);
                                    $low = mysqli_stmt_get_result($low_stmt);
                                    $low_count = mysqli_fetch_assoc($low);
                                    echo $low_count['count'];
                                    mysqli_stmt_close($low_stmt);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-danger text-white">
                            <div class="card-body">
                                <h5>Out of Stock</h5>
                                <h3><?php 
                                    $out_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM products WHERE stock = 0");
                                    mysqli_stmt_execute($out_stmt);
                                    $out = mysqli_stmt_get_result($out_stmt);
                                    $out_count = mysqli_fetch_assoc($out);
                                    echo $out_count['count'];
                                    mysqli_stmt_close($out_stmt);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Low Stock Alert -->
                <?php if(mysqli_num_rows($low_stock) > 0){ ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert!</strong> 
                    <?php echo mysqli_num_rows($low_stock); ?> products are running low on stock.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php } ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Seller</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($products)){ ?>
                                    <tr class="<?php echo $row['stock'] == 0 ? 'table-danger' : ($row['stock'] < 10 ? 'table-warning' : ''); ?>">
                                        <td>
                                            <?php if($row['image']){ ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" width="50" height="50" class="rounded">
                                            <?php } else { ?>
                                            <span class="text-muted"><i class="fas fa-image fa-2x"></i></span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['seller_name']); ?></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                                <input type="number" name="stock" class="form-control form-control-sm d-inline-block" style="width: 80px;" value="<?php echo $row['stock']; ?>" min="0" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $row['stock'] == 0 ? 'bg-danger' : ($row['stock'] < 10 ? 'bg-warning' : 'bg-success'); ?>">
                                                <?php echo $row['stock'] == 0 ? 'Out of Stock' : ($row['stock'] < 10 ? 'Low Stock' : 'In Stock'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info"><i class="fas fa-history"></i></a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
