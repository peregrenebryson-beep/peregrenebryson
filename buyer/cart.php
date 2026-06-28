<?php

include "../includes/session_config.php";
include "../config/database.php";

if(!isset($_SESSION['id']) || $_SESSION['role'] != 'buyer'){
    header("Location: ../login.php");
    exit();
}

include "../includes/csrf.php";
$csrf_token = generateCsrfToken();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart - APFIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="index.php">APFIMS - Buyer</a>
            <div>
                <a href="index.php" class="btn btn-light me-2">Products</a>
                <a href="orders.php" class="btn btn-light me-2">My Orders</a>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5">
        <h2>Shopping Cart</h2>
        
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Cart Items</h4>
                    </div>
                    <div class="card-body" id="cart-items">
                        <!-- Cart items will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span id="subtotal">TZS 0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span id="total">TZS 0.00</span>
                        </div>
                        <hr>
                        <form action="place_order.php" method="POST" id="checkout-form">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="cart" id="cart-data">
                            <div class="mb-3">
                                <label>Shipping Address</label>
                                <textarea name="shipping_address" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Place Order</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    function loadCart(){
        const container = document.getElementById('cart-items');
        
        if(cart.length === 0){
            container.innerHTML = '<p class="text-muted">Your cart is empty.</p>';
            document.getElementById('subtotal').textContent = 'TZS 0.00';
            document.getElementById('total').textContent = 'TZS 0.00';
            return;
        }
        
        // Fetch product details
        const productIds = cart.map(item => item.id).join(',');
        fetch(`get_cart_products.php?ids=${productIds}`)
            .then(response => response.json())
            .then(data => {
                let html = '';
                let total = 0;
                
                data.forEach(product => {
                    const cartItem = cart.find(item => item.id === product.id);
                    const itemTotal = product.price * cartItem.quantity;
                    total += itemTotal;
                    
                    html += `
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2 cart-item\" data-id="${product.id}">
                            <div>
                                <h5>${product.name}</h5>
                                <p class="text-muted">TZS ${product.price.toFixed(2)} x ${cartItem.quantity}</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold me-3">TZS ${itemTotal.toFixed(2)}</span>
                                <button class="btn btn-sm btn-danger" onclick="removeFromCart(${product.id})">Remove</button>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
                document.getElementById('subtotal').textContent = `TZS ${total.toFixed(2)}`;
                document.getElementById('total').textContent = `TZS ${total.toFixed(2)}`;
            });
    }
    
    function removeFromCart(productId){
        cart = cart.filter(item => item.id !== productId);
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    }
    
    document.getElementById('checkout-form').addEventListener('submit', function(e){
        e.preventDefault();
        document.getElementById('cart-data').value = JSON.stringify(cart);
        
        const formData = new FormData(this);
        fetch('place_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success){
                localStorage.removeItem('cart');
                alert('Order placed successfully! Order ID: ' + data.order_id);
                window.location.href = 'orders.php';
            } else {
                alert('Error: ' + data.error);
            }
        });
    });
    
    loadCart();
    </script>
</body>
</html>
