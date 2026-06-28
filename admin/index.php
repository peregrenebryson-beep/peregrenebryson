<?php

include "../includes/session_config.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

include "../config/database.php";

// Get total users using prepared statement
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM users");
mysqli_stmt_execute($stmt);
$users_count = mysqli_stmt_get_result($stmt);
$users = mysqli_fetch_assoc($users_count);
mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - APFIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">APFIMS - Admin</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>
    
    <div class="container mt-5">
        <h2>Welcome, <?php echo $_SESSION['fullname']; ?></h2>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text display-4"><?php echo $users['count']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Manage Users</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = mysqli_prepare($conn, "SELECT id, fullname, email, role FROM users");
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        while($row = mysqli_fetch_assoc($result)){
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['role']); ?></td>
                            <td>
                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                        <?php } 
                        mysqli_stmt_close($stmt);
                        ?>
                    </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Products</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Seller</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = mysqli_prepare($conn, "SELECT p.*, u.fullname as seller_name FROM products p LEFT JOIN users u ON p.seller_id = u.id ORDER BY p.created_at DESC LIMIT 5");
                                mysqli_stmt_execute($stmt);
                                $products = mysqli_stmt_get_result($stmt);
                                while($prod = mysqli_fetch_assoc($products)){
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(substr($prod['name'], 0, 20)); ?>...</td>
                                    <td><?php echo htmlspecialchars($prod['seller_name']); ?></td>
                                    <td>TZS <?php echo number_format($prod['price'], 2); ?></td>
                                    <td><?php echo $prod['stock']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $prod['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo $prod['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php } mysqli_stmt_close($stmt); ?>
                            </tbody>
                        </table>
                        <a href="products.php" class="btn btn-primary btn-sm">View All Products</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Orders</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Buyer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = mysqli_prepare($conn, "SELECT o.*, u.fullname as buyer_name FROM orders o LEFT JOIN users u ON o.buyer_id = u.id ORDER BY o.created_at DESC LIMIT 5");
                                mysqli_stmt_execute($stmt);
                                $orders = mysqli_stmt_get_result($stmt);
                                while($ord = mysqli_fetch_assoc($orders)){
                                ?>
                                <tr>
                                    <td>#<?php echo $ord['id']; ?></td>
                                    <td><?php echo htmlspecialchars($ord['buyer_name']); ?></td>
                                    <td>TZS <?php echo number_format($ord['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            $status_colors = ['pending' => 'warning', 'confirmed' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'];
                                            echo $status_colors[$ord['status']] ?? 'secondary';
                                        ?>">
                                            <?php echo ucfirst($ord['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d', strtotime($ord['created_at'])); ?></td>
                                </tr>
                                <?php } mysqli_stmt_close($stmt); ?>
                            </tbody>
                        </table>
                        <a href="orders.php" class="btn btn-primary btn-sm">View All Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
