<?php

include "../includes/session_config.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

include "../config/database.php";

// Get current page
$current_page = basename($_SERVER['PHP_SELF']);

// Get dashboard stats
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM users");
mysqli_stmt_execute($stmt);
$users_count = mysqli_stmt_get_result($stmt);
$users = mysqli_fetch_assoc($users_count);
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM products");
mysqli_stmt_execute($stmt);
$products_count = mysqli_stmt_get_result($stmt);
$products = mysqli_fetch_assoc($products_count);
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM orders");
mysqli_stmt_execute($stmt);
$orders_count = mysqli_stmt_get_result($stmt);
$orders = mysqli_fetch_assoc($orders_count);
mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APFIMS - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .sidebar .nav-link i {
            width: 25px;
        }
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4 text-white text-center border-bottom border-white-20">
                    <h4><i class="fas fa-seedling me-2"></i>APFIMS</h4>
                    <small>Agricultural Platform</small>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link <?php echo $current_page == 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                        <i class="fas fa-tags"></i> Categories
                    </a>
                    <a class="nav-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>" href="products.php">
                        <i class="fas fa-boxes"></i> Mazao & Pembejeo
                    </a>
                    <a class="nav-link <?php echo $current_page == 'customers.php' ? 'active' : ''; ?>" href="customers.php">
                        <i class="fas fa-users"></i> Customers
                    </a>
                    <a class="nav-link <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                    <a class="nav-link <?php echo $current_page == 'payments.php' ? 'active' : ''; ?>" href="payments.php">
                        <i class="fas fa-credit-card"></i> Payments
                    </a>
                    <a class="nav-link <?php echo $current_page == 'inventory.php' ? 'active' : ''; ?>" href="inventory.php">
                        <i class="fas fa-warehouse"></i> Inventory
                    </a>
                    <a class="nav-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                    <a class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>" href="users.php">
                        <i class="fas fa-user-cog"></i> Users
                    </a>
                    <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </nav>
                <div class="mt-auto p-4">
                    <a href="../logout.php" class="btn btn-danger w-100">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content p-4">
