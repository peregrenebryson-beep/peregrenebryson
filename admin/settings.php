<?php
include 'layout.php';
include '../includes/csrf.php';
$csrf_token = generateCsrfToken();

// Handle settings update
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
        die("Invalid request");
    }
    
    $site_name = htmlspecialchars(trim($_POST['site_name']));
    $site_email = htmlspecialchars(trim($_POST['site_email']));
    $site_phone = htmlspecialchars(trim($_POST['site_phone']));
    $currency = htmlspecialchars(trim($_POST['currency']));
    
    // For demo, just show success (in real app, save to database)
    echo "<script>alert('Settings updated successfully!');</script>";
}
?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-cog me-2"></i>Settings</h2>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>General Settings</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    
                                    <div class="mb-3">
                                        <label>Site Name</label>
                                        <input type="text" name="site_name" class="form-control" value="APFIMS - Agricultural Platform">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label>Site Email</label>
                                        <input type="email" name="site_email" class="form-control" value="info@apfims.com">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label>Site Phone</label>
                                        <input type="text" name="site_phone" class="form-control" value="+255 XXX XXX XXX">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label>Currency</label>
                                        <select name="currency" class="form-select">
                                            <option value="TZS" selected>Tanzanian Shilling (TZS)</option>
                                            <option value="USD">US Dollar (USD)</option>
                                            <option value="EUR">Euro (EUR)</option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Save Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>System Info</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
                                <p><strong>MySQL Version:</strong> <?php echo mysqli_get_server_info($conn); ?></p>
                                <p><strong>Server Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5>Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <a href="setup_tables.php" class="btn btn-warning w-100 mb-2">
                                    <i class="fas fa-database me-2"></i>Reset Database Tables
                                </a>
                                <a href="setup_payments.php" class="btn btn-info w-100 mb-2">
                                    <i class="fas fa-credit-card me-2"></i>Setup Payments Table
                                </a>
                                <a href="../logout.php" class="btn btn-danger w-100">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
