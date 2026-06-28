<?php

include "../includes/session_config.php";
include "../config/database.php";
include "../includes/csrf.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

// Validate order ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid order ID");
}

$order_id = intval($_GET['id']);
$csrf_token = generateCsrfToken();

// Get current order status
$stmt = mysqli_prepare($conn, "SELECT status FROM orders WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if(!$order){
    die("Order not found");
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
        die("Invalid request");
    }
    
    $new_status = $_POST['status'];
    
    if(!in_array($new_status, ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'])){
        die("Invalid status");
    }
    
    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
    
    if(mysqli_stmt_execute($stmt)){
        header("Location: orders.php");
        exit();
    } else {
        die("Failed to update order status");
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Order Status - APFIMS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">APFIMS - Admin</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>
    
    <div class="container mt-5">
        <h2>Update Order Status #<?php echo $order_id; ?></h2>
        <a href="orders.php" class="btn btn-secondary mb-3">Back to Orders</a>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Current Status: <?php echo ucfirst($order['status']); ?></h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="mb-3">
                                <label>New Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php mysqli_stmt_close($stmt); ?>
