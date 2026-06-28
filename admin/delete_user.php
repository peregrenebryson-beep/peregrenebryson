<?php

include "../includes/session_config.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

include "../config/database.php";

// Validate ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid user ID");
}

$id = intval($_GET['id']);

// Prevent admin from deleting themselves
if($id == $_SESSION['id']){
    header("Location: index.php");
    exit();
}

// Use prepared statement
$sql = "DELETE FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);

if(mysqli_stmt_execute($stmt)){
    header("Location: index.php");
    exit();
} else {
    die("Failed to delete user");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
