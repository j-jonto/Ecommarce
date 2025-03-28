<?php
$pageTitle = "Shopping Cart";
require_once '../includes/header.php';

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update quantity
    if (isset($_POST['update_quantity']) && isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $productId = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        updateCartItem($productId, $quantity);
        
        // Redirect to avoid form resubmission
        header("Location: " . SITE_URL . "/cart.php");
        exit;
    }
    
    // Remove item
    if (isset($_POST['remove_item']) && isset($_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];
        
        removeCartItem($productId);
        
        // Redirect to avoid form resubmission
        header("Location: " . SITE_URL . "/cart.php");
        exit;
    }
    
    // Clear cart
    if (isset($_POST['clear_cart'])) {
        clearCart();
        
        // Redirect to avoid form resubmission
        header("Location: " . SITE_URL . "/cart.php");
        exit;
    }
}

// Get cart items
$cartItems = getCartItems();
$cartTotal = getCartTotal();
?>

<h1>Shopping Cart</h1>

<?php if (count($cartItems) > 0): ?>
    <div class="cart-content">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td>
                            <div class="cart-product">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="<?php echo SITE_URL; ?>/assets/uploads/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-product-image">
                                <?php endif; ?>
                                <div class="cart-product-info">
                                    <a href="<?php echo SITE_URL; ?>/product.php?id=<?php echo $item['id']; ?>">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td><?php echo formatPrice($item['price']); ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="99" class="cart-quantity">
                                <button type="submit" name="update_quantity" class="btn btn-sm">Update</button>
                            </form>
                        </td>
                        <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="remove_item" class="btn btn-sm btn-accent">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="cart-actions">
            <form method="post" action="">
                <button type="submit" name="clear_cart" class="btn btn-secondary">Clear Cart</button>
            </form>
            
            <div class="cart-total">
                <strong>Total: <?php echo formatPrice($cartTotal); ?></strong>
            </div>
            
            <a href="<?php echo SITE_URL; ?>/checkout.php" class="btn btn-accent">Proceed to Checkout</a>
        </div>
    </div>
<?php else: ?>
    <div class="empty-cart">
        <p>Your shopping cart is empty.</p>
        <a href="<?php echo SITE_URL; ?>" class="btn">Continue Shopping</a>
    </div>
<?php endif; ?>

<style>
.cart-product {
    display: flex;
    align-items: center;
}

.cart-product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 15px;
}

.cart-product-info {
    flex: 1;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.85rem;
}

.empty-cart {
    text-align: center;
    padding: 50px 0;
}

.empty-cart p {
    margin-bottom: 20px;
    font-size: 1.2rem;
    color: #666;
}
</style>

<?php require_once '../includes/footer.php'; ?>