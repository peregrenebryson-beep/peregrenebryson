<?php

include "includes/session_config.php";

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

// Redirect based on role
if($_SESSION['role'] == 'admin'){
    header("Location: admin/index.php");
} elseif($_SESSION['role'] == 'seller'){
    header("Location: seller/index.php");
} elseif($_SESSION['role'] == 'buyer'){
    header("Location: buyer/index.php");
}

exit();

?>