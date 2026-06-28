<?php

include "../config/database.php";

$id = $_GET['id'];

$sql = "DELETE FROM categories WHERE id=$id";

mysqli_query($conn, $sql);

header("Location: categories.php");

?>