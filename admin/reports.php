<?php
include 'layout.php';

// Get report data
// Sales by month
$sales_stmt = mysqli_prepare($conn, "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as total, COUNT(*) as orders FROM orders WHERE status = 'delivered' GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month DESC LIMIT 12");
mysqli_stmt_execute($sales_stmt);
$sales_data = mysqli_stmt_get_result($sales_stmt);

// Top selling products
$top_stmt = mysqli_prepare($conn, "SELECT p.name, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as revenue FROM order_items oi JOIN products p ON oi.product_id = p.id GROUP BY p.id ORDER BY total_sold DESC LIMIT 10");
mysqli_stmt_execute($top_stmt);
$top_products = mysqli_stmt_get_result($top_stmt);

// Top customers
$customer_stmt = mysqli_prepare($conn, "SELECT u.fullname, COUNT(o.id) as orders, SUM(o.total_amount) as spent FROM users u JOIN orders o ON u.id = o.buyer_id WHERE o.status = 'delivered' GROUP BY u.id ORDER BY spent DESC LIMIT 10");
mysqli_stmt_execute($customer_stmt);
$top_customers = mysqli_stmt_get_result($customer_stmt);
?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-chart-bar me-2"></i>Reports</h2>
                    <div>
                        <select class="form-select d-inline-block" style="width: auto;">
                            <option>Last 30 Days</option>
                            <option>Last 7 Days</option>
                            <option>This Month</option>
                            <option>This Year</option>
                        </select>
                        <button class="btn btn-primary"><i class="fas fa-download me-2"></i>Export</button>
                    </div>
                </div>
                
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5>Total Revenue</h5>
                                <h3>TZS <?php 
                                    $rev_stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status = 'delivered'");
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
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5>Total Orders</h5>
                                <h3><?php 
                                    $ord_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM orders");
                                    mysqli_stmt_execute($ord_stmt);
                                    $ord = mysqli_stmt_get_result($ord_stmt);
                                    $orders = mysqli_fetch_assoc($ord);
                                    echo $orders['count'];
                                    mysqli_stmt_close($ord_stmt);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5>Delivered</h5>
                                <h3><?php 
                                    $del_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM orders WHERE status = 'delivered'");
                                    mysqli_stmt_execute($del_stmt);
                                    $del = mysqli_stmt_get_result($del_stmt);
                                    $delivered = mysqli_fetch_assoc($del);
                                    echo $delivered['count'];
                                    mysqli_stmt_close($del_stmt);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <h5>Pending</h5>
                                <h3><?php 
                                    $pend_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
                                    mysqli_stmt_execute($pend_stmt);
                                    $pend = mysqli_stmt_get_result($pend_stmt);
                                    $pending = mysqli_fetch_assoc($pend);
                                    echo $pending['count'];
                                    mysqli_stmt_close($pend_stmt);
                                ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-line me-2"></i>Sales by Month</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>Orders</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row = mysqli_fetch_assoc($sales_data)){ ?>
                                            <tr>
                                                <td><?php echo $row['month']; ?></td>
                                                <td><?php echo $row['orders']; ?></td>
                                                <td>TZS <?php echo number_format($row['total'], 2); ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-trophy me-2"></i>Top Selling Products</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Sold</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row = mysqli_fetch_assoc($top_products)){ ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars(substr($row['name'], 0, 30)); ?>...</td>
                                                <td><?php echo $row['total_sold']; ?></td>
                                                <td>TZS <?php echo number_format($row['revenue'], 2); ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-star me-2"></i>Top Customers</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Total Orders</th>
                                                <th>Total Spent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row = mysqli_fetch_assoc($top_customers)){ ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                                <td><?php echo $row['orders']; ?></td>
                                                <td>TZS <?php echo number_format($row['spent'], 2); ?></td>
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
