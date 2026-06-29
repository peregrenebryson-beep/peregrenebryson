<?php
session_start();
include "config/database.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){

$user = mysqli_fetch_assoc($result);

if(password_verify($password, $user['password'])){

$_SESSION['id'] = $user['id'];
$_SESSION['fullname'] = $user['fullname'];
$_SESSION['role'] = $user['role'];

header("Location: admin/dashboard.php");
exit();

}else{
echo "Wrong password";
}

}else{
echo "User not found";
}
?>