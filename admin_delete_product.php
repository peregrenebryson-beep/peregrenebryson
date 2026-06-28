<?php

include "../config/database.php";

$id = $_GET['id'];

// optional: delete image later
$sql = "DELETE FROM products WHERE id=$id";

mysqli_query($conn, $sql);

header("Location: products.php");

?>