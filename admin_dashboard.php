<?php include "header.php"; ?>

<h2>Admin Dashboard</h2>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?></p>

<div class="row">

<div class="col-md-3">
<div class="card p-3 bg-success text-white">
    <h5>Products</h5>
</div>
</div>

<div class="col-md-3">
<div class="card p-3 bg-primary text-white">
    <h5>Orders</h5>
</div>
</div>

<div class="col-md-3">
<div class="card p-3 bg-warning text-dark">
    <h5>Customers</h5>
</div>
</div>

<div class="col-md-3">
<div class="card p-3 bg-danger text-white">
    <h5>Reports</h5>
</div>
</div>

</div>

<?php include "footer.php"; ?>