<?php
$pageTitle = "Order Confirmation";
require_once '../includes/header.php';

// Check if order ID exists in session
if (!isset($_SESSION['last_order_id'])) {
    // Redirect to homepage if no order
    header("Location: " . SITE_URL);
    exit;
}

$orderId = $_SESSION['last_order_id'];

// Get order details
$query = "SELECT o.*, c.name, c.email, c.phone, c.address
          FROM orders o
          JOIN customers c ON o.customer_id = c.id
          WHERE o.id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $orderId);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Get order items
$query = "SELECT oi.*, p.name, p.image_path
          FROM order_items oi
          JOIN products p ON oi.product_id = p.id
          WHERE oi.order_id = :order_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':order_id', $orderId);
$stmt->execute();
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Clear the order ID from session
unset($_SESSION['last_order_id']);
?>

<div class="confirmation-container">
    <div class="confirmation-header">
        <div class="confirmation-icon">✓</div>
        <h1>Your Order Has Been Received</h1>
        <p>Thank you for your purchase! Your order has been placed and is being processed.</p>
    </div>
    
    <div class="confirmation-details">
        <div class="confirmation-section">
            <h2>Order Details</h2>
            <div class="detail-line">
                <span>Order Number:</span>
                <strong>#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></strong>
            </div>
            <div class="detail-line">
                <span>Order Date:</span>
                <strong><?php echo date('F j, Y', strtotime($order['order_date'])); ?></strong>
            </div>
            <div class="detail-line">
                <span>Order Status:</span>
                <strong><?php echo ucfirst($order['status']); ?></strong>
            </div>
            <div class="detail-line">
                <span>Total:</span>
                <strong><?php echo formatPrice($order['total_amount']); ?></strong>
            </div>
        </div>
        
        <div class="confirmation-section">
            <h2>Customer Information</h2>
            <div class="detail-line">
                <span>Name:</span>
                <strong><?php echo htmlspecialchars($order['name']); ?></strong>
            </div>
            <div class="detail-line">
                <span>Email:</span>
                <strong><?php echo htmlspecialchars($order['email']); ?></strong>
            </div>
            <div class="detail-line">
                <span>Phone:</span>
                <strong><?php echo htmlspecialchars($order['phone']); ?></strong>
            </div>
            <div class="detail-line">
                <span>Address:</span>
                <strong><?php echo htmlspecialchars($order['address']); ?></strong>
            </div>
        </div>
    </div>
    
    <div class="confirmation-products">
        <h2>Order Items</h2>
        <table class="order-items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td>
                            <div class="order-product">
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="<?php echo SITE_URL; ?>/assets/uploads/<?php echo $item['image_path']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="order-product-image">
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($item['name']); ?></span>
                            </div>
                        </td>
                        <td><?php echo formatPrice($item['price_at_time_of_order']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo formatPrice($item['price_at_time_of_order'] * $item['quantity']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Total</td>
                    <td><strong><?php echo formatPrice($order['total_amount']); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="confirmation-actions">
        <p>We'll contact you shortly to confirm your order details and arrange delivery.</p>
        <a href="<?php echo SITE_URL; ?>" class="btn">Return to Home</a>
    </div>
</div>

<style>
.confirmation-container {
    max-width: 800px;
    margin: 0 auto;
}

.confirmation-header {
    text-align: center;
    margin-bottom: 40px;
}

.confirmation-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background-color: var(--success-color);
    color: white;
    border-radius: 50%;
    font-size: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.confirmation-details {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    margin-bottom: 40px;
}

.confirmation-section {
    flex: 1;
    min-width: 250px;
    background-color: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
}

.confirmation-section h2 {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border-color);
}

.detail-line {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.confirmation-products {
    margin-bottom: 40px;
}

.order-items-table {
    width: 100%;
    border-collapse: collapse;
}

.order-items-table th,
.order-items-table td {
    padding: 12px;
    border-bottom: 1px solid var(--border-color);
}

.order-product {
    display: flex;
    align-items: center;
}

.order-product-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 10px;
}

.confirmation-actions {
    text-align: center;
    margin-top: 40px;
}

.confirmation-actions p {
    margin-bottom: 20px;
}
</style>

<?php require_once '../includes/footer.php'; ?>