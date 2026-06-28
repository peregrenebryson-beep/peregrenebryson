<?php

include "../includes/session_config.php";
include "../config/database.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'buyer'){
    header("Location: ../login.php");
    exit();
}

// Validate order ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid order ID");
}

$order_id = intval($_GET['id']);

// Verify order belongs to this buyer
$stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ? AND buyer_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $order_id, $_SESSION['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if(!$order){
    die("Order not found or unauthorized");
}

// Get order items
$stmt = mysqli_prepare($conn, "SELECT oi.*, p.name as product_name, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details - APFIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="index.php">APFIMS - Buyer</a>
            <div>
                <a href="orders.php" class="btn btn-light me-2">My Orders</a>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5">
        <h2>Order Details #<?php echo $order['id']; ?></h2>
        
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Order Items</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($item = mysqli_fetch_assoc($items_result)){ ?>
                                    <tr>
                                        <td>
                                            <?php if($item['image']){ ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" width="50" height="50" class="me-2">
                                            <?php } ?>
                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                        </td>
                                        <td>TZS <?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>TZS <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td class="fw-bold">TZS <?php echo number_format($order['total_amount'], 2); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Order Information</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Status:</strong> 
                            <span class="badge bg-<?php 
                                $status_colors = [
                                    'pending' => 'warning',
                                    'confirmed' => 'info',
                                    'shipped' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                echo $status_colors[$order['status']] ?? 'secondary';
                            ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </p>
                        <p><strong>Date:</strong> <?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></p>
                        <p><strong>Shipping Address:</strong><br><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php 
mysqli_stmt_close($stmt);
mysqli_stmt_close($items_result);
?>
