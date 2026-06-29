<?php

include "config/database.php";

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid product ID");
}

$id = intval($_GET['id']);

// Delete product using prepared statement
$sql = "DELETE FROM products WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: admin_product.php");

?>