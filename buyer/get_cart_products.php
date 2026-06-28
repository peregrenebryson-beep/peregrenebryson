<?php

include "../includes/session_config.php";
include "../config/database.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'buyer'){
    echo json_encode([]);
    exit();
}

if(!isset($_GET['ids'])){
    echo json_encode([]);
    exit();
}

$ids = explode(',', $_GET['ids']);
$ids = array_map('intval', $ids);
$ids = array_filter($ids);

if(empty($ids)){
    echo json_encode([]);
    exit();
}

$placeholders = str_repeat('?,', count($ids) - 1) . '?';
$types = str_repeat('i', count($ids));

$stmt = mysqli_prepare($conn, "SELECT id, name, price FROM products WHERE id IN ($placeholders) AND status = 'active' AND stock > 0");
mysqli_stmt_bind_param($stmt, $types, ...$ids);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
while($row = mysqli_fetch_assoc($result)){
    $products[] = $row;
}

echo json_encode($products);

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
