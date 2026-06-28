<?php
include 'layout.php';
include '../includes/csrf.php';
$csrf_token = generateCsrfToken();

// Handle delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ? AND role = 'buyer'");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: customers.php");
    exit();
}

// Get all customers (buyers)
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE role = 'buyer' ORDER BY created_at DESC");
mysqli_stmt_execute($stmt);
$customers = mysqli_stmt_get_result($stmt);
?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-users me-2"></i>Customers</h2>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search customers...">
                        <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Total Orders</th>
                                        <th>Total Spent</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($customers)){ 
                                        // Get order count
                                        $order_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM orders WHERE buyer_id = ?");
                                        mysqli_stmt_bind_param($order_stmt, "i", $row['id']);
                                        mysqli_stmt_execute($order_stmt);
                                        $order_result = mysqli_stmt_get_result($order_stmt);
                                        $order_count = mysqli_fetch_assoc($order_result);
                                        mysqli_stmt_close($order_stmt);
                                        
                                        // Get total spent
                                        $spent_stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE buyer_id = ? AND status = 'delivered'");
                                        mysqli_stmt_bind_param($spent_stmt, "i", $row['id']);
                                        mysqli_stmt_execute($spent_stmt);
                                        $spent_result = mysqli_stmt_get_result($spent_stmt);
                                        $total_spent = mysqli_fetch_assoc($spent_result);
                                        mysqli_stmt_close($spent_stmt);
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><span class="badge bg-primary"><?php echo $order_count['count']; ?></span></td>
                                        <td>TZS <?php echo number_format($total_spent['total'], 2); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            <a href="customers.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
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
