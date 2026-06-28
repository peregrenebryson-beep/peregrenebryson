<?php
include 'layout.php';
include '../includes/csrf.php';
$csrf_token = generateCsrfToken();

// Handle delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: products.php");
    exit();
}

// Get all products
$stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name, u.fullname as seller_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN users u ON p.seller_id = u.id ORDER BY p.created_at DESC");
mysqli_stmt_execute($stmt);
$products = mysqli_stmt_get_result($stmt);
?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-boxes me-2"></i>Mazao & Pembejeo</h2>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search products...">
                        <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Seller</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($products)){ ?>
                                    <tr>
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
                                        <td>TZS <?php echo number_format($row['price'], 2); ?></td>
                                        <td>
                                            <span class="badge <?php echo $row['stock'] > 10 ? 'bg-success' : ($row['stock'] > 0 ? 'bg-warning' : 'bg-danger'); ?>">
                                                <?php echo $row['stock']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                            <a href="products.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
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
