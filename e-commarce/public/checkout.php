<?php
$pageTitle = "Checkout";
require_once '../includes/header.php';

// Check if cart is empty
$cartItems = getCartItems();
if (count($cartItems) === 0) {
    // Redirect to cart page if empty
    header("Location: " . SITE_URL . "/cart.php");
    exit;
}

$cartTotal = getCartTotal();
$errors = [];
$success = false;

// Process checkout form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';
    $address = isset($_POST['address']) ? sanitize($_POST['address']) : '';
    
    // Basic validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required.";
    }
    
    // Process order if no errors
    if (empty($errors)) {
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            // Insert customer data
            $query = "INSERT INTO customers (name, email, phone, address) 
                      VALUES (:name, :email, :phone, :address)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->execute();
            
            $customerId = $pdo->lastInsertId();
            
            // Create new order
            $query = "INSERT INTO orders (customer_id, total_amount, status) 
                      VALUES (:customer_id, :total_amount, 'new')";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':customer_id', $customerId);
            $stmt->bindParam(':total_amount', $cartTotal);
            $stmt->execute();
            
            $orderId = $pdo->lastInsertId();
            
            // Insert order items
            $query = "INSERT INTO order_items (order_id, product_id, quantity, price_at_time_of_order) 
                      VALUES (:order_id, :product_id, :quantity, :price)";
            $stmt = $pdo->prepare($query);
            
            foreach ($cartItems as $item) {
                $stmt->bindParam(':order_id', $orderId);
                $stmt->bindParam(':product_id', $item['id']);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':price', $item['price']);
                $stmt->execute();
            }
            
            // Commit transaction
            $pdo->commit();
            
            // Store order ID in session for confirmation page
            $_SESSION['last_order_id'] = $orderId;
            
            // Clear cart
            clearCart();
            
            // Redirect to confirmation page
            header("Location: " . SITE_URL . "/order_confirmation.php");
            exit;
        } catch (PDOException $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            $errors[] = "An error occurred while processing your order. Please try again.";
            
            if (DEBUG_MODE) {
                $errors[] = $e->getMessage();
            }
        }
    }
}
?>

<h1>Checkout</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="checkout-container">
    <div class="checkout-form">
        <h2>Shipping Information</h2>
        <form method="post" action="" id="checkout-form">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="address" class="form-label">Full Address</label>
                <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="notes" class="form-label">Order Notes (optional)</label>
                <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-accent btn-lg">Place Order</button>
        </form>
    </div>
    
    <div class="order-summary">
        <h2 class="summary-title">Order Summary</h2>
        
        <?php foreach ($cartItems as $item): ?>
            <div class="summary-line">
                <span><?php echo htmlspecialchars($item['name']); ?> (<?php echo $item['quantity']; ?>)</span>
                <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
            </div>
        <?php endforeach; ?>
        
        <div class="summary-line summary-total">
            <span>Total</span>
            <span><?php echo formatPrice($cartTotal); ?></span>
        </div>
    </div>
</div>

<style>
.btn-lg {
    padding: 12px 24px;
    font-size: 1.1rem;
}
</style>

<?php require_once '../includes/footer.php'; ?>