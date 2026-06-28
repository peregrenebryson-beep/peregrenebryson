<?php
include 'layout.php';
include '../includes/csrf.php';
$csrf_token = generateCsrfToken();

// Handle payment status update
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
        die("Invalid request");
    }
    
    $payment_id = intval($_POST['payment_id']);
    $status = $_POST['status'];
    
    if($status == 'completed'){
        $paid_at = date('Y-m-d H:i:s');
        $stmt = mysqli_prepare($conn, "UPDATE payments SET payment_status = ?, paid_at = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $status, $paid_at, $payment_id);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE payments SET payment_status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $payment_id);
    }
    
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: payments.php");
    exit();
}

// Get all payments
$stmt = mysqli_prepare($conn, "SELECT p.*, o.id as order_id, u.fullname as buyer_name FROM payments p LEFT JOIN orders o ON p.order_id = o.id LEFT JOIN users u ON o.buyer_id = u.id ORDER BY p.created_at DESC");
mysqli_stmt_execute($stmt);
$payments = mysqli_stmt_get_result($stmt);
?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-credit-card me-2"></i>Payments</h2>
                    <div>
                        <select class="form-select d-inline-block" style="width: auto;">
                            <option>All Status</option>
                            <option>Pending</option>
                            <option>Completed</option>
                            <option>Failed</option>
                            <option>Refunded</option>
                        </select>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5>Total Revenue</h5>
                                <h3>TZS <?php 
                                    $rev_stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE payment_status = 'completed'");
                                    mysqli_stmt_execute($rev_stmt);
                                    $rev = mysqli_stmt_get_result($rev_stmt);
                                    $revenue = mysqli_fetch_assoc($rev);
                                    echo number_format($revenue['total'], 2);
                                    mysqli_stmt_close($rev_stmt);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <h5>Pending</h5>
                                <h3><?php 
                                    $pend_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM payments WHERE payment_status = 'pending'");
                                    mysqli_stmt_execute($pend_stmt);
                                    $pend = mysqli_stmt_get_result($pend_stmt);
                                    $pending = mysqli_fetch_assoc($pend);
                                    echo $pending['count'];
                                    mysqli_stmt_close($pend_stmt);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5>Completed</h5>
                                <h3><?php 
                                    $comp_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM payments WHERE payment_status = 'completed'");
                                    mysqli_stmt_execute($comp_stmt);
                                    $comp = mysqli_stmt_get_result($comp_stmt);
                                    $completed = mysqli_fetch_assoc($comp);
                                    echo $completed['count'];
                                    mysqli_stmt_close($comp_stmt);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-danger text-white">
                            <div class="card-body">
                                <h5>Failed</h5>
                                <h3><?php 
                                    $fail_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM payments WHERE payment_status = 'failed'");
                                    mysqli_stmt_execute($fail_stmt);
                                    $fail = mysqli_stmt_get_result($fail_stmt);
                                    $failed = mysqli_fetch_assoc($fail);
                                    echo $failed['count'];
                                    mysqli_stmt_close($fail_stmt);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Order ID</th>
                                        <th>Buyer</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Transaction ID</th>
                                        <th>Paid At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($payments)){ ?>
                                    <tr>
                                        <td>#<?php echo $row['id']; ?></td>
                                        <td>#<?php echo $row['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                                        <td>TZS <?php echo number_format($row['amount'], 2); ?></td>
                                        <td><?php echo ucfirst($row['payment_method']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                $status_colors = ['pending' => 'warning', 'completed' => 'success', 'failed' => 'danger', 'refunded' => 'secondary'];
                                                echo $status_colors[$row['payment_status']] ?? 'secondary';
                                            ?>">
                                                <?php echo ucfirst($row['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $row['transaction_id'] ? htmlspecialchars($row['transaction_id']) : '-'; ?></td>
                                        <td><?php echo $row['paid_at'] ? date('Y-m-d H:i', strtotime($row['paid_at'])) : '-'; ?></td>
                                        <td>
                                            <?php if($row['payment_status'] == 'pending'){ ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="payment_id" value="<?php echo $row['id']; ?>">
                                                <select name="status" class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="this.form.submit()">
                                                    <option value="pending" selected>Pending</option>
                                                    <option value="completed">Complete</option>
                                                    <option value="failed">Failed</option>
                                                </select>
                                            </form>
                                            <?php } else { ?>
                                            <span class="text-muted">-</span>
                                            <?php } ?>
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
