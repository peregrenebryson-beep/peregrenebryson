<?php
include 'layout.php';
include '../includes/csrf.php';
$csrf_token = generateCsrfToken();

// Handle delete with CSRF protection
if(isset($_POST['delete'])){
    if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
        die("Invalid request");
    }
    $id = intval($_POST['delete']);
    if($id != $_SESSION['id']){
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: users.php");
    exit();
}

// Get all users
$stmt = mysqli_prepare($conn, "SELECT * FROM users ORDER BY created_at DESC");
mysqli_stmt_execute($stmt);
$users = mysqli_stmt_get_result($stmt);
?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-user-cog me-2"></i>Users</h2>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search users...">
                        <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($users)){ ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['role'] == 'admin' ? 'danger' : ($row['role'] == 'seller' ? 'success' : 'primary'); ?>">
                                                <?php echo ucfirst($row['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            <?php if($row['id'] != $_SESSION['id']){ ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="delete" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
