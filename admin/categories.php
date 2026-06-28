<?php
include 'layout.php';
include '../includes/csrf.php';
$csrf_token = generateCsrfToken();

// Handle add/edit category
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])){
        die("Invalid request");
    }
    
    $name = htmlspecialchars(trim($_POST['name']));
    $description = htmlspecialchars(trim($_POST['description']));
    
    if(isset($_POST['category_id']) && !empty($_POST['category_id'])){
        // Update
        $id = intval($_POST['category_id']);
        $stmt = mysqli_prepare($conn, "UPDATE categories SET name = ?, description = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        // Insert
        $stmt = mysqli_prepare($conn, "INSERT INTO categories (name, description) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $name, $description);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    header("Location: categories.php");
    exit();
}

// Handle delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: categories.php");
    exit();
}

// Get all categories
$stmt = mysqli_prepare($conn, "SELECT * FROM categories ORDER BY name ASC");
mysqli_stmt_execute($stmt);
$categories = mysqli_stmt_get_result($stmt);

// Get category for edit
$edit_category = null;
if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);
    $stmt = mysqli_prepare($conn, "SELECT * FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $edit_category = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-tags me-2"></i>Categories</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                        <i class="fas fa-plus me-2"></i>Add Category
                    </button>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Products Count</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($cat = mysqli_fetch_assoc($categories)){ 
                                        // Get products count
                                        $count_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM products WHERE category_id = ?");
                                        mysqli_stmt_bind_param($count_stmt, "i", $cat['id']);
                                        mysqli_stmt_execute($count_stmt);
                                        $count_result = mysqli_stmt_get_result($count_stmt);
                                        $count = mysqli_fetch_assoc($count_result);
                                        mysqli_stmt_close($count_stmt);
                                    ?>
                                    <tr>
                                        <td><?php echo $cat['id']; ?></td>
                                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($cat['description'], 0, 50)); ?>...</td>
                                        <td><span class="badge bg-info"><?php echo $count['count']; ?></span></td>
                                        <td><?php echo date('Y-m-d', strtotime($cat['created_at'])); ?></td>
                                        <td>
                                            <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="categories.php?delete=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Add/Edit Modal -->
                <div class="modal fade" id="categoryModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?php echo $edit_category ? 'Edit Category' : 'Add Category'; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <?php if($edit_category){ ?>
                                <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                                <?php } ?>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Category Name</label>
                                        <input type="text" name="name" class="form-control" required 
                                               value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" rows="3"><?php echo $edit_category ? htmlspecialchars($edit_category['description']) : ''; ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary"><?php echo $edit_category ? 'Update' : 'Save'; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <?php if($edit_category){ ?>
                <script>
                document.addEventListener('DOMContentLoaded', function(){
                    var modal = new bootstrap.Modal(document.getElementById('categoryModal'));
                    modal.show();
                });
                </script>
                <?php } ?>
                
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
