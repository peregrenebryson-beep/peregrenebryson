<?php
include 'layout.php';
include '../includes/csrf.php';
$csrf_token = generateCsrfToken();

// Get all orders
$stmt = mysqli_prepare($conn, "SELECT o.*, u.fullname as buyer_name FROM orders o LEFT JOIN users u ON o.buyer_id = u.id ORDER BY o.created_at DESC");
mysqli_stmt_execute($stmt);
$orders = mysqli_stmt_get_result($stmt);
?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-shopping-cart me-2"></i>Orders</h2>
                    <div>
                        <select class="form-select d-inline-block" style="width: auto;">
                            <option>All Status</option>
                            <option>Pending</option>
                            <option>Confirmed</option>
                            <option>Shipped</option>
                            <option>Delivered</option>
                            <option>Cancelled</option>
                        </select>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Buyer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Phone</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($orders)){ ?>
                                    <tr>
                                        <td>#<?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                                        <td>TZS <?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                $status_colors = ['pending' => 'warning', 'confirmed' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'];
                                                echo $status_colors[$row['status']] ?? 'secondary';
                                            ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="order_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                            <a href="update_order_status.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
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
