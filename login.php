<?php
include "includes/session_config.php";
include "includes/csrf.php";
$csrf_token = generateCsrfToken();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - APFIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container">

<div class="row justify-content-center">

<div class="col-md-4 mt-5">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h4>System Login</h4>

</div>

<div class="card-body">

<form action="login_process.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    
    <button class="btn btn-success w-100">Login</button>
</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>