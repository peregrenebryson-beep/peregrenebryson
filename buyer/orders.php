<?php

include "../includes/session_config.php";
include "../config/database.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'buyer'){
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders - APFIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="index.php">APFIMS - Buyer</a>
            <div>
                <a href="index.php" class="btn btn-light me-2">Products</a>
                <a href="cart.php" class="btn btn-light me-2">Cart</a>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5">
        <h2>My Orders</h2>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Order History</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmt = mysqli_prepare($conn, "SELECT o.*, (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count FROM orders o WHERE o.buyer_id = ? ORDER BY o.created_at DESC");
                        mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        
                        if(mysqli_num_rows($result) > 0){
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Items</th>
                                        <th>Shipping Address</th>
                                        <th>Phone</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)){ ?>
                                    <tr>
                                        <td>#<?php echo $row['id']; ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                                        <td>TZS <?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                $status_colors = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'info',
                                                    'shipped' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                echo $status_colors[$row['status']] ?? 'secondary';
                                            ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $row['item_count']; ?></td>
                                        <td><?php echo htmlspecialchars(substr($row['shipping_address'], 0, 30)); ?>...</td>
                                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                        <td>
                                            <a href="order_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } else { ?>
                        <p class="text-muted">No orders found.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php mysqli_stmt_close($stmt); ?>
