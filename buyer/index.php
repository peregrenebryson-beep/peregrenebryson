<?php

include "../includes/session_config.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'buyer'){
    header("Location: ../login.php");
    exit();
}

include "../config/database.php";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Buyer Dashboard - APFIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="#">APFIMS - Buyer</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>
    
    <div class="container mt-5">
        <h2>Welcome, <?php echo $_SESSION['fullname']; ?></h2>
        <p class="text-muted">Buyer Dashboard</p>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Available Products</h4>
                        <a href="cart.php" class="btn btn-info">View Cart (<span id="cart-count">0</span>)</a>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <select id="category-filter" class="form-select" onchange="filterProducts()">
                                <option value="">All Categories</option>
                                <?php
                                $cat_stmt = mysqli_prepare($conn, "SELECT id, name FROM categories");
                                mysqli_stmt_execute($cat_stmt);
                                $categories = mysqli_stmt_get_result($cat_stmt);
                                while($cat = mysqli_fetch_assoc($categories)){
                                ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php } mysqli_stmt_close($cat_stmt); ?>
                            </select>
                        </div>
                        
                        <div class="row" id="products-container">
                            <?php
                            $stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name, u.fullname as seller_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN users u ON p.seller_id = u.id WHERE p.status = 'active' AND p.stock > 0 ORDER BY p.created_at DESC");
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            
                            if(mysqli_num_rows($result) > 0){
                                while($row = mysqli_fetch_assoc($result)){
                            ?>
                            <div class="col-md-3 mb-4 product-item" data-category="<?php echo $row['category_id']; ?>">
                                <div class="card h-100">
                                    <?php if($row['image']){ ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" height="150" style="object-fit: cover;">
                                    <?php } else { ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" height="150">No Image</div>
                                    <?php } ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                        <p class="card-text text-muted small"><?php echo htmlspecialchars($row['category_name']); ?></p>
                                        <p class="card-text"><?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?>...</p>
                                        <p class="card-text fw-bold">TZS <?php echo number_format($row['price'], 2); ?></p>
                                        <p class="card-text small">Seller: <?php echo htmlspecialchars($row['seller_name']); ?></p>
                                        <p class="card-text small">Stock: <?php echo $row['stock']; ?></p>
                                        <button class="btn btn-primary w-100" onclick="addToCart(<?php echo $row['id']; ?>)">Add to Cart</button>
                                    </div>
                                </div>
                            </div>
                            <?php }
                            } else { ?>
                            <div class="col-12">
                                <p class="text-muted">No products available.</p>
                            </div>
                            <?php } mysqli_stmt_close($stmt); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        function filterProducts(){
            const categoryId = document.getElementById('category-filter').value;
            const items = document.querySelectorAll('.product-item');
            
            items.forEach(item => {
                if(categoryId === '' || item.dataset.category === categoryId){
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        function addToCart(productId){
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const existing = cart.find(item => item.id === productId);
            
            if(existing){
                existing.quantity++;
            } else {
                cart.push({id: productId, quantity: 1});
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            alert('Product added to cart!');
        }
        
        function updateCartCount(){
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const count = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cart-count').textContent = count;
        }
        
        updateCartCount();
        </script>
    </div>
</body>
</html>
