<?php
include "includes/session_config.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - APFIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
        }
        .sidebar {
            height: 100vh;
            width: 220px;
            position: fixed;
            background: #198754;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #145c32;
        }
        .content {
            margin-left: 230px;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-white text-center">APFIMS</h4>
    <hr class="text-white">

    <a href="dashboard.php">Dashboard</a>
    <a href="categories.php">Categories</a>
    <a href="products.php">Products</a>
    <a href="customers.php">Customers</a>
    <a href="orders.php">Orders</a>
    <a href="payments.php">Payments</a>
    <a href="inventory.php">Inventory</a>
    <a href="reports.php">Reports</a>
    <a href="users.php">Users</a>
    <a href="settings.php">Settings</a>
    <a href="../logout.php" style="color: yellow;">Logout</a>
</div>

<div class="content">